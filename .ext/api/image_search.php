<?php
include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

if (!isset($_GET["search_type"]) || empty($_GET["search_type"])) {
    http_response_code(400);
    $result = new Result(Result::DENIED, "Hiányos keresési típus!");
    echo $result->toJSON();
    return;
}

// Felhasználó adatainak lekérése
$isLoggedIn = false;
$user = getUserData();
if ($user->isSuccess()) {
    $isLoggedIn=true;
    $user = $user->message[0];
}

if (!$isLoggedIn) {
    http_response_code(400);
    $result = new Result(Result::DENIED, "Csak bejelentkezett felhasználó indíthat kéréseket!");
    echo $result->toJSON();
    exit();
}

if ($user["role"] != "Administrator") {
    http_response_code(405);
    $result = new Result(Result::DENIED, "Csak adminisztrátor indíthat kéréseket!");
    echo $result->toJSON();
    exit();
}

$searchType = $_GET['search_type'];

switch ($searchType) {
    case 'category':
        if (!isset($_GET["id"]) || !isset($_GET["type"]) || empty($_GET["id"]) || empty($_GET["type"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "get_category_images.php";
        break;
    
    case 'product':
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "get_product_images.php";
        break;
    default:
        http_response_code(400);
        $result = new Result(Result::DENIED, "Ismeretlen keresési típus!");
        echo $result->toJSON();
        break;
}