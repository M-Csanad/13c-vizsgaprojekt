<?php
include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['response']) || !is_bool($data['response'])) {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Üres vagy hibás paraméter');
    echo $result->toJSON();
    exit();
}

$response = $data['response'];
session_start();

if ($response === true) {
    $result = getUserData();
    if (!$result->isSuccess()) {
        http_response_code(404);
        $result = new Result(Result::ERROR, 'Nem található felhasználó');
        echo $result->toJSON();
    }
    $user = $result->message[0];
    
    $cart = json_decode($_COOKIE['cart'], true);
    foreach($cart as $item) {
        $result = updateData('INSERT INTO cart(user_id, product_id, quantity, page_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), modified_at = NOW();', [intval($user['id']), intval($item['product_id']), intval($item['quantity']), intval($item['page_id'])], 'iiii');
        if (!$result->isSuccess()) {
            http_response_code(500);
            echo $result->toJSON();
            exit();
        }
    }

    removeCartCookieSession();
}
else {
    removeCartCookieSession();
}

$result = new Result(Result::SUCCESS, 'Sikeres művelet');
echo $result->toJSON();