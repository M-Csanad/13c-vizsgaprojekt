<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode("Hibás metódus! Várt: GET, Aktuális: ".$_SERVER["REQUEST_METHOD"], JSON_UNESCAPED_UNICODE);
    header("bad request", true, 400);
    return;
}

if (!isset($_POST["search_type"]) || empty($_POST["search_type"])) {
    header("bad request", true, 400);
    echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
    return;
}

$searchType = $_POST['search_type'];

switch ($searchType) {
    case 'category':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once $_SERVER["DOCUMENT_ROOT"]."/../../../.ext/misc/search_category.php";
        break;
    
    case 'product_page':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once $_SERVER["DOCUMENT_ROOT"]."/../../../.ext/misc/search_product_page.php";
        break;

    case 'product':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once $_SERVER["DOCUMENT_ROOT"]."/../../../.ext/misc/search_product.php";
        break;

    case 'user':
        if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once $_SERVER["DOCUMENT_ROOT"]."/../../../.ext/misc/search_user.php";
        break;
    
    case 'get_categories':
        if (!isset($_POST["table"]) || empty($_POST["table"])) {
            header("bad request", true, 400);
            echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
            return;
        }

        include_once $_SERVER["DOCUMENT_ROOT"]."/../../../.ext/misc/get_categories.php";
        break;
    default:
        # code...
        break;
}