<?php
include_once __DIR__.'/../classes/inputvalidator.php';
include_once __DIR__.'/../autofill_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

// Bemeneti értékek ellenőrzése
$fields = ["type", "autofill-name", "zip", "city", "street-house"];
$values = [];
$rules = [
    "type" => [
        "rule" => function ($e) {return $e === "delivery" || $e === "billing"; },
        "message" => "Hibás típus."
    ],
    "autofill-name" => [
        "rule" => function ($e) { return mb_strlen($e) > 0; },
        "message" => "Hibás cím."
    ], 
    "zip" => [
        "rule" => '/^[1-9]{1}[0-9]{3}$/',
        "message" => "Hibás irányítószám."
    ],
    "city" => [
        "rule" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
        "message" => "Hibás település"
    ],
    "street_house" => [
        "rule" => '/^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+ [a-záéíóöőúüű]{2,} \d{1,}(?:\/[A-Z]+)?$/',
        "message" => "Hibás utca és házszám."
    ]
];

// Ellenőrizzük, hogy minden adat megvan-e
foreach ($fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Hiányos kérés.');
        echo $result->toJSON();
        exit();
    }
    
    $values[$field] = $_POST[$field];
}

// Validáljuk az elemeket
$validator = new InputValidator($values, $rules);
if ($validator->test()->isError()) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Hibás adat.');
    echo $result->toJSON();
    exit();
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
$values["user_id"] = $user['id'];

// Adatbázisba feltöltés
$result = uploadAutofill($values);

http_response_code($result->isSuccess() ? 200: 400);
echo $result->messageJSON();
exit();