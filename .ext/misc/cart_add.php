<?php
include_once __DIR__."/../init.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, "Hibás metódus! Várt: POST");
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['product_id'], $data['quantity'])) {
    
    echo "ok";
} else {
    http_response_code(400);
    echo "not ok";
}