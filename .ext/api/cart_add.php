<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['url']) && isset($data['qty'])) {
    $segments = explode('/', ltrim($data['url'], '/'));

    // Ha nem 3 elemű az URL, akkor biztos, hogy nem termék
    if (count($segments) !== 3) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Hibás URL');
        echo $result->toJSON();
        exit();
    }

    // URL adatok kinyerése
    $parents = array_slice($segments, 0, 2);
    $product = array_slice($segments, 2, 1)[0];

    // Termék azonosító lekérése
    $result = isValidProduct($product, $parents);
    if ($result->isError()) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Hibás lekérdezés');
        echo $result->toJSON();
        exit();
    }

    if ($result->isEmpty()) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Ismeretlen termék');
        echo $result->toJSON();
        exit();
    }

    $productId = $result->message[0]['id'];
    $pageId = $result->message[0]['page_id'];
    
    // A termék lekérdezése
    $result = selectData("SELECT CAST(product.stock AS INT) AS stock, product.name, CAST(product.unit_price AS INT) AS unit_price, 
        product_page.link_slug, 
        ( 
            SELECT DISTINCT image.uri FROM image INNER JOIN product_image ON product_image.image_id=image.id 
            WHERE product_image.product_id=? AND image.uri LIKE '%thumbnail%' LIMIT 1
        ) AS thumbnail_uri  
        FROM product_page INNER JOIN product ON product_page.product_id=product.id 
        WHERE product_page.id=?", [$productId, $pageId], 'ii');
    if (!$result->isSuccess()) {
        http_response_code(404);
        $result = new Result(Result::ERROR, 'Nem található a termék.');
        echo $result->toJSON();
        exit();
    }
    $product = $result->message[0];
    
    // Ellenőrizzük a mennyiséget
    $stock = $product['stock'];
    $requestedQuantity = $data['qty'];

    // Ellenőrizzük, hogy már van-e ilyen termék a kosárban
    $existingQuantity = 0;
    
    // Felhasználó adatainak lekérése, ha van
    $isLoggedIn = false;
    $user = getUserData();
    if ($user->isSuccess()) {
        $isLoggedIn = true;
        $user = $user->message[0];
        
        // Ha be van jelentkezve, lekérdezzük az adatbázisból a meglévő mennyiséget
        $existingCartItem = selectData(
            "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?", 
            [intval($user['id']), intval($productId)], 
            'ii'
        );
        
        if ($existingCartItem->isSuccess() && !$existingCartItem->isEmpty()) {
            $existingQuantity = $existingCartItem->message[0]['quantity'];
        }
    } else if (isset($_SESSION['cart'])) {
        // Vendég esetén a session-ből ellenőrizzük
        foreach ($_SESSION['cart'] as $item) {
            if ($item['product_id'] == $productId) {
                $existingQuantity = $item['quantity'];
                break;
            }
        }
    }

    // A meglévő és az új mennyiség összege
    $totalQuantity = $existingQuantity + $requestedQuantity;

    // Ellenőrizzük, hogy a teljes mennyiség nem haladja-e meg a készletet
    if ($totalQuantity > $stock) {
        http_response_code(400);
        $result = new Result(
            Result::ERROR, 
            sprintf(
                'Nem lehet %d darabot a kosárhoz adni ebből a termékből.', 
                $requestedQuantity, $existingQuantity, $stock
            )
        );
        echo $result->toJSON();
        exit();
    }

    if ($requestedQuantity < 1) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'A megadott mennyiség nem megfelelő.');
        echo $result->toJSON();
        exit();
    }

    if (!isset($_SESSION['cart'])) {
        http_response_code(404);
        $result = new Result(Result::ERROR, 'Nem található a kosár.');
        echo $result->toJSON();
        exit();
    }
    
    // Ha be van jelentkezve, akkor az adatbázissal és a sessionnel dolgozunk
    if ($isLoggedIn) {
        // Adatbázis frissítése
        $result = updateData('INSERT INTO cart(user_id, product_id, quantity, page_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), modified_at = NOW();', [intval($user['id']), intval($productId), intval($requestedQuantity), intval($pageId)], 'iiii');
        if (!$result->isSuccess()) {
            http_response_code(500);
            echo $result->toJSON();
            exit();
        }
        
        $result = new Result(Result::SUCCESS, 'Sikeresen hozzáadva');
        echo $result->toJSON();
    }
    // Ha nincs bejelentkezve, akkor sütikkel és sessionnel dolgozunk
    else {
        // Session kosár frissítése
        $foundExistingRow = false;
        foreach ($_SESSION['cart'] as &$row) {
            if ($row['product_id'] == $productId) {
                $row['quantity'] += $requestedQuantity;
                $row['modified_at'] = date("Y-m-d H:i:s", time());
                $foundExistingRow = true;
                break;
            }
        }
        unset($row); // Referenciát töröljük, mivel PHP-ban megmaradna
    
        // Ha még nem volt olyan termék a kosárban, akkor hozzáadjuk
        if (!$foundExistingRow) {
            $_SESSION['cart'][] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'quantity' => $requestedQuantity,
                'unit_price' => $product['unit_price'],
                'stock' => $stock,
                'link_slug' => $product['link_slug'],
                'page_id' => $pageId,
                'thumbnail_uri' => $product['thumbnail_uri'],
                'created_at' => date("Y-m-d H:i:s", time()),
                'modified_at' => date("Y-m-d H:i:s", time()),
            ];
        }

        setCartCookie();

        $result = new Result(Result::SUCCESS, 'Sikeresen hozzáadva');
        echo $result->toJSON();
    }
    
} else {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Hiányos kérés');
    echo $result->toJSON();
    exit();
}