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

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $result = new Result(Result::ERROR, 'Üres kosár!');
    echo $result->toJSON();
    exit();
}

// Create arrays for the query
$productIds = [];
$quantities = [];
$types = "";
$params = [];

// Prepare data for the query
foreach ($_SESSION['cart'] as $item) {
    $productIds[] = $item['product_id'];
    $quantities[$item['product_id']] = $item['quantity'];
    $types .= "i";
    $params[] = $item['product_id'];
}

// Build the CASE statement for comparing each product's quantity
$cases = [];
foreach ($quantities as $id => $qty) {
    $cases[] = "WHEN id = $id THEN $qty";
}
$caseStatement = "CASE " . implode(" ", $cases) . " END";

// Create the query to find products where stock is less than requested quantity
$query = "SELECT id as product_id, stock 
          FROM product 
          WHERE id IN (" . str_repeat("?,", count($productIds) - 1) . "?) 
          AND stock < " . $caseStatement;

$stock_query = selectData($query, $params, $types);

if ($stock_query->isError()) {
    http_response_code(500);
    echo json_encode(new Result(Result::ERROR, "Adatbázis hiba!"));
    exit();
}

if (!$stock_query->isEmpty()) {
    $result = new Result(Result::ERROR, $stock_query->message);
} else {
    $result = new Result(Result::SUCCESS, null);
}

echo $result->toJSON();
