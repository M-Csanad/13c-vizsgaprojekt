<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

session_start();

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
    
        $result = new Result(Result::SUCCESS, $_COOKIE['cart']);
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
            product.name, product.unit_price, product_page.link_slug, product_page.id
            FROM cart INNER JOIN product ON cart.product_id=product.id 
            INNER JOIN product_page ON cart.page_id=product_page.id 
            WHERE cart.user_id=?;", intval($user['id']), "i");

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

        $result = new Result(Result::EMPTY, "A kosár üres.");
        echo $result->toJSON();
    }
}