<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['url'])) {
    $segments = explode('/', ltrim($data['url'], '/'));

    // Ha nem 3 elemű az URL, akkor biztos, hogy nem termék
    if (count($segments) !== 3) {
        http_response_code(405);
        $result = new Result(Result::ERROR, 'Hibás URL');
        echo $result->toJSON();
        exit();
    }

    // URL adatok kinyerése
    $parents = array_slice($segments, 0, 2);
    $product = array_slice($segments, 2, 1)[0];

    // Termék azonosító lekérése
    $result = isValidProduct($product, $parents);
    if ($result->isError()) {
        http_response_code(405);
        $result = new Result(Result::ERROR, 'Hibás lekérdezés');
        echo $result->toJSON();
        exit();
    }

    if ($result->isEmpty()) {
        http_response_code(405);
        $result = new Result(Result::ERROR, 'Ismeretlen termék');
        echo $result->toJSON();
        exit();
    }

    $productId = $result->message[0]['id'];

    // Felhasználó adatainak lekérése, ha van
    $isLoggedIn = false;
    $user = getUserData();
    if ($user->isSuccess()) $isLoggedIn=true;
    
    // TODO cart tábla feltöltése
    echo json_encode($user);
    
} else {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hiányos kérés');
    echo $result->toJSON();
    exit();
}