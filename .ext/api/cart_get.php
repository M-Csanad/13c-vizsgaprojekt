<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: GET');
    echo $result->toJSON();
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Felhasználó adatainak lekérése, ha van
$isLoggedIn = false;
$user = getUserData();
if ($user->isSuccess()) {
    $isLoggedIn=true;
    $user = $user->message[0];
}

// Segédfüggvény a készletértékek frissítéséhez a kosár adataiban
function updateStockValues($cartData) {
    if (empty($cartData)) return [];
    
    // Az összes termékazonosító lekérése
    $productIds = array_map(function($item) {
        return $item['product_id'];
    }, $cartData);
    
    // Lekérdezés összeállítása a friss készletértékek lekéréséhez
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $types = str_repeat('i', count($productIds));
    
    $stockResult = selectData(
        "SELECT id, stock FROM product WHERE id IN ($placeholders)",
        $productIds,
        $types
    );
    
    if ($stockResult->isError() || $stockResult->isEmpty()) {
        return $cartData;
    }
    
    // Készletadatok keresési tömbjének létrehozása
    $stockData = array_column($stockResult->message, 'stock', 'id');
    
    // Készletértékek frissítése a kosár adataiban
    foreach ($cartData as &$item) {
        if (isset($stockData[$item['product_id']])) {
            $item['stock'] = $stockData[$item['product_id']];
        }
    }
    
    return $cartData;
}

// Ha a Sessionben, vagy sütiben van kosár tartalom, akkor azt adjuk vissza.
if (isset($_SESSION['cart']) && !empty($_SESSION['cart']) && !$isLoggedIn) {
    $_SESSION['cart'] = updateStockValues($_SESSION['cart']);
    
    if (!isset($_COOKIE['cart']) || empty(json_decode($_COOKIE['cart'], true))) {
        setCartCookie();
    }

    $result = new Result(Result::SUCCESS, $_SESSION['cart']);
    echo $result->toJSON();
    exit();
}
else if (isset($_COOKIE['cart']) && !empty(json_decode($_COOKIE['cart'], true))) {
    $cartData = json_decode($_COOKIE['cart'], true);

    if (!$isLoggedIn) {
        $cartData = updateStockValues($cartData);
        $_SESSION['cart'] = $cartData;
        setCartCookie();
        
        $result = new Result(Result::SUCCESS, $cartData);
        echo $result->toJSON();
        exit();
    }
    else {
        $result = new Result(Result::PROMPT, [
            "title" => "Kosár összevonása",
            "description" => "A vendégként létrehozott kosara tartalmát hozzá tudjuk adni a fiókjához. Össze kivánja vonni őket?"
        ]);
        echo $result->toJSON();
    }
}
else {
    if ($isLoggedIn) {
        $result = selectData("SELECT CAST(cart.product_id AS INT) AS product_id, 
            CAST(cart.quantity AS INT) AS quantity, CAST(product.stock AS INT) AS stock, CAST(product.net_weight AS INT) AS net_weight,
            cart.created_at, cart.modified_at,
            product.name, product.unit_price, product_page.link_slug, cart.page_id, 
            ( 
                SELECT DISTINCT image.uri FROM image INNER JOIN product_image ON product_image.image_id=image.id 
                WHERE product_image.product_id=cart.product_id AND image.uri LIKE '%thumbnail%' LIMIT 1
            ) AS thumbnail_uri 
            FROM cart INNER JOIN product ON cart.product_id=product.id 
            INNER JOIN product_page ON cart.page_id=product_page.id 
            WHERE cart.user_id=?
            ORDER BY product.name;", intval($user['id']), "i");
                    
        if ($result->isError()) {
            http_response_code(500);
            echo json_encode(new Result(Result::ERROR, "Hiba merült fel a kosár lekérdezése során."));
            exit();
        }

        // Ha nincs még az adatbázisban kosár tartalom, akkor létrehozunk egy üreset
        if ($result->isEmpty()) {
            $_SESSION['cart'] = [];
        }
        else {
            // Egyéb esetben pedig csak hozzárendeljük a Sessionhöz az adatbázis tartalmát
            $_SESSION['cart'] = $result->message;
        }

        $result = new Result(Result::SUCCESS, $_SESSION['cart']);
        echo $result->toJSON();
    }
    else {
        $_SESSION['cart'] = [];
        setCartCookie();
        
        $result = new Result(Result::SUCCESS, $_SESSION['cart']);
        echo $result->toJSON();
    }
}