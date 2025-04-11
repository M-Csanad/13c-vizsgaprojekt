<?php
include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

if (!isset($_POST["search_type"]) || empty($_POST["search_type"])) {
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

$searchType = $_POST['search_type'];

switch ($searchType) {
    case 'category':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "search_category.php";
        break;
    
    case 'product_page':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "search_product_page.php";
        break;

    case 'product':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "search_product.php";
        break;

    case 'user':
    case 'user_data':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "search_user.php";
        break;
    
    case 'get_categories':
        if (!isset($_POST["table"]) || empty($_POST["table"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "get_categories.php";
        break;

    case 'order':
        if (!isset($_POST["search_term"]) || is_null($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once "search_order.php";
        break;
    default:
        # code...
        break;
}