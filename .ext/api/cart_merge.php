<?php
include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

session_start();

$result = getUserData();
if (!$result->isSuccess()) {
    http_response_code(404);
    $result = new Result(Result::ERROR, 'Nem található felhasználó');
    echo $result->toJSON();
}

$cart = $_COOKIE['cart'];
foreach($cart as $item) {
    $result = updateData('INSERT INTO cart(user_id, product_id, quantity, page_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), modified_at = NOW();', [intval($user['id']), intval($item['product_id']), intval($item['quantity']), intval($item['page_id'])], 'iiii');
    if (!$result->isSuccess()) {
        http_response_code(500);
        echo $result->toJSON();
        exit();
    }
}

$result = new Result(Result::SUCCESS, 'Sikeres összevonás');
echo $result->toJSON();