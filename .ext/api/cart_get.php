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

// Ha a Sessionben, vagy sütiben van kosár tartalom, akkor azt adjuk vissza.
if (isset($_SESSION['cart']) && !empty($_SESSION['cart']) && !$isLoggedIn) {

    if (!isset($_COOKIE['cart']) || empty(json_decode($_COOKIE['cart'], true))) {
        setCartCookie();
    }

    $result = new Result(Result::SUCCESS, $_SESSION['cart']);
    echo $result->toJSON();
}
else if (isset($_COOKIE['cart']) && !empty(json_decode($_COOKIE['cart'], true))) {

    $cartData = json_decode($_COOKIE['cart'], true);

    if (!$isLoggedIn) {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            $_SESSION['cart'] = $cartData;
        }
        
        $result = new Result(Result::SUCCESS, $cartData);
        echo $result->toJSON();
    }
    else {
        $result = new Result(Result::PROMPT, [
            "title" => "Kosár összevonása",
            "description" => "A vendégként létrehozott kosarad tartalmát hozzá tudjuk adni a fiókodhoz. Összevonod őket?"
        ]);
        echo $result->toJSON();
    }
}
else {
    if ($isLoggedIn) {
        $result = selectData("SELECT CAST(cart.product_id AS INT) AS product_id, 
            CAST(cart.quantity AS INT) AS quantity, CAST(product.stock AS INT) AS stock, cart.created_at, cart.modified_at,
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