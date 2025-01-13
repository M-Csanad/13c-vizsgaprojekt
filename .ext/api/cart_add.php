<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['url']) && isset($data['qty'])) {
    $segments = explode('/', ltrim($data['url'], '/'));

    // Ha nem 3 elemű az URL, akkor biztos, hogy nem termék
    if (count($segments) !== 3) {
        http_response_code(405);
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
        http_response_code(405);
        $result = new Result(Result::ERROR, 'Hibás lekérdezés');
        echo $result->toJSON();
        exit();
    }

    if ($result->isEmpty()) {
        http_response_code(405);
        $result = new Result(Result::ERROR, 'Ismeretlen termék');
        echo $result->toJSON();
        exit();
    }

    $productId = $result->message[0]['id'];
    
    // A készlet lekérdezése
    $result = selectData('SELECT CAST(stock AS INT) AS stock FROM product WHERE product.id=?', $productId, 'i');
    if (!$result->isSuccess()) {
        http_response_code(404);
        $result = new Result(Result::ERROR, 'Nem található a készlet.');
        echo $result->toJSON();
        exit();
    }
    
    // Ellenőrizzük a mennyiséget
    $stock = $result->message[0]['stock'];
    $quantity = $data['qty'];

    if ($quantity > $stock || $quantity < 1) {
        http_response_code(405);
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

    // Felhasználó adatainak lekérése, ha van
    $isLoggedIn = false;
    $user = getUserData();
    if ($user->isSuccess()) {
        $isLoggedIn=true;
        $user = $user->message[0];
    }
    
    // Ha be van jelentkezve, akkor az adatbázissal és a sessionnel dolgozunk
    if ($isLoggedIn) {

        // Adatbázis frissítése
        $result = updateData('INSERT INTO cart(user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), modified_at = NOW();', [intval($user['id']), intval($productId), intval($quantity)], 'iii');
        if (!$result->isSuccess()) {
            http_response_code(500);
            echo $result->toJSON();
            exit();
        }
        

        // Session kosár frissítése
        $foundExistingRow = false;
        foreach ($_SESSION['cart'] as &$row) {
            if ($row['user_id'] == $user['id'] && $row['product_id'] == $productId) {
                $row['quantity'] += $quantity;
                $foundExistingRow = true;
                break;
            }
        }
        unset($row); // Referenciát töröljük, mivel PHP-ban megmaradna

        // Ha még nem volt olyan termék a kosárban, akkor hozzáadjuk
        if (!$foundExistingRow) {
            $_SESSION['cart'][] = [
                'user_id' => $user['id'],
                'product_id' => $productId,
                'quantity' => $quantity
            ];
        }

        $result = new Result(Result::SUCCESS, 'Very good my brother');
        echo $result->toJSON();
    }
    // Ha nincs bejelentkezve, akkor sütikkel és sessionnel dolgozunk
    else {
        http_response_code(501);
        $result = new Result(Result::ERROR, 'Vendégeket még nem fogadok');
        echo $result->toJSON();
        exit();
    }
    
} else {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hiányos kérés');
    echo $result->toJSON();
    exit();
}