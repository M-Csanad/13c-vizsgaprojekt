<?php
include_once __DIR__.'/../autofill_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: GET');
    echo $result->toJSON();
    exit();
}

if (!isset($_GET['type']) || $_GET['type'] !== 'delivery' && $_GET['type'] !== "billing" && $_GET['type'] !== "all") {
    http_response_code(400);
    $result = new Result(Result::ERROR, "Hiányos vagy hibás típus.");
    echo $result->toJSON();
    exit();
}
$type = $_GET['type'];

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
$userId = $user['id'];

$result = $type == "all" ? getAllAutofill($userId) : getAutofill($type, $userId);
if (!$result->isSuccess()) {
    http_response_code(400);
    echo $result->toJSON();
    exit();
}

echo $result->messageJSON();