<?php
include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['operation']) || !isset($data['product_id']) || !is_int($data['product_id'])) {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Üres vagy hibás paraméter');
    echo $result->toJSON();
    exit();
}

$operation = $data['operation'];
$productId = $data['product_id'];

$user = null;
$isLoggedIn = false;
$result = getUserData();
if ($result->isSuccess()) {
    $user = $result->message[0];
    $isLoggedIn=true;
}

if ($operation === '+' || $operation === '-') {
    $quantityChange = ($operation === '+') ? 1 : -1;

    // Ha be van jelentkezve, akkor az adatbázist módosítjuk
    if ($isLoggedIn) {
        $result = selectData("SELECT product.stock, cart.quantity FROM product INNER JOIN cart ON cart.product_id=product.id WHERE cart.product_id=? AND cart.user_id=?;", [$productId, $user['id']], 'ii');
        if (!$result->isSuccess()) {
            http_response_code(500);
            echo $result->toJSON();
            exit();
        }
        $stock = $result->message[0]['stock'];
        $currentQuantity = $result->message[0]['quantity'];

        if ($currentQuantity + $quantityChange > $stock || $currentQuantity + $quantityChange < 1) return;

        $result = updateData("UPDATE cart SET quantity=quantity+? WHERE product_id=? AND user_id=?", [$quantityChange, $productId, $user['id']], 'iii');
        if (!$result->isSuccess()) {
            http_response_code(500);
            echo $result->toJSON();
            exit();
        }
    }
    // Ha nincs bejelentkezve, akkor a Sessiont és a sütit módosítjuk
    else {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] === $productId) {
                if ($item['quantity'] + $quantityChange > $item['stock'] || $item['quantity'] + $quantityChange < 1) return;

                $item['quantity'] += $quantityChange;
                break;
            }
        }
        unset($item);

        setCartCookie();
    }
}