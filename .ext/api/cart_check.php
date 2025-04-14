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

// Hozzon létre tömböket a lekérdezéshez
$productIds = [];
$quantities = [];
$types = "";
$params = [];

// Adatok előkészítése a lekérdezéshez
foreach ($_SESSION['cart'] as $item) {
    $productIds[] = $item['product_id'];
    $quantities[$item['product_id']] = $item['quantity'];
    $types .= "i";
    $params[] = $item['product_id'];
}

// CASE utasítás összeállítása az egyes termékek mennyiségének összehasonlításához
$cases = [];
foreach ($quantities as $id => $qty) {
    $cases[] = "WHEN id = $id THEN $qty";
}
$caseStatement = "CASE " . implode(" ", $cases) . " END";

// Lekérdezés létrehozása azoknak a termékeknek a megtalálásához, ahol a készlet kevesebb, mint a kért mennyiség
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
