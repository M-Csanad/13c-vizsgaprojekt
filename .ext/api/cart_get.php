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

// Ha a Sessionben van kosár tartalom, akkor azt adjuk vissza.
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE);
}
else {
    // Felhasználó adatainak lekérése, ha van
    $isLoggedIn = false;
    $user = getUserData();
    if ($user->isSuccess()) {
        $isLoggedIn=true;
        $user = $user->message[0];
    }

    if ($isLoggedIn) {
        $result = selectData("SELECT CAST(user_id AS INT) AS user_id, session_id, CAST(product_id AS INT) AS product_id, CAST(quantity AS INT) AS quantity, created_at, modified_at FROM cart WHERE cart.user_id=?;", intval($user['id']), "i");
        if ($result->isError()) {
            http_response_code(500);
            echo json_encode(new Result(Result::ERROR, "Hiba merült fel a kosár lekérdezése során."));
            exit();
        }

        // Ha nincs még az adatbázisban kosár tartalom, akkor létrehozunk egy üreset
        if ($result->isEmpty()) {
            $_SESSION['cart'] = [];
        }

        // Egyéb esetben pedig csak hozzárendeljük a Sessionhöz az adatbázis tartalmát
        $_SESSION['cart'] = $result->message;
        echo json_encode($_SESSION['cart'], JSON_UNESCAPED_UNICODE);
    }
    else {
        $_SESSION['cart'] = [];
        echo json_encode(new Result(Result::EMPTY, "A kosár üres."));
    }
}