<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../cart_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['product_id']) || !is_int($data['product_id'])) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Üres vagy hibás paraméter');
    echo $result->toJSON();
    exit();
}

$productId = $data['product_id'];
$delta = isset($data['delta']) ? intval($data['delta']) : 0;
if (!$delta) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Nincs delta érték megadva!');
    echo $result->toJSON();
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Aktuális mennyiség lekérdezése
$currentQuantity = getCurrentQuantity($productId);

// Készletellenőrzés
$stockCheck = checkProductStock($productId, $currentQuantity + $delta);
if ($stockCheck->isError()) {
    http_response_code(400);
    echo $stockCheck->toJSON();
    exit();
}

// Ha idáig eljutottunk, frissíthetjük a mennyiséget
$isLoggedIn = false;
$user = getUserData();
if ($user->isSuccess()) {
    $isLoggedIn = true;
    $user = $user->message[0];
}

// Frissítés bejelentkezett felhasználónál
if ($isLoggedIn) {
    $result = updateData(
        "UPDATE cart SET quantity=quantity+? WHERE product_id=? AND user_id=?",
        [$delta, $productId, $user['id']],
        'iii'
    );
    if (!$result->isSuccess()) {
        http_response_code(500);
        echo $result->toJSON();
        exit();
    }
} else {
    // Session kosár frissítése
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] === $productId) {
            $item['quantity'] += $delta;
            break;
        }
    }
    unset($item);
    setCartCookie();
}

$result = new Result(Result::SUCCESS, null);
echo $result->toJSON();