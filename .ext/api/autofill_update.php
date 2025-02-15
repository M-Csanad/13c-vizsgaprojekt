<?php
include_once __DIR__.'/../classes/inputvalidator.php';
include_once __DIR__.'/../autofill_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

// Bemeneti értékek ellenőrzése
$fields = ["id", "type", "autofill-name", "zip", "city", "street-house"];
$values = [];
$rules = [
    "id" => [
        "rule" => function ($e) { return intval($e) && $e > 0; },
        "message" => "Hibás azonosító."
    ],
    "type" => [
        "rule" => function ($e) { return $e === "delivery" || $e === "billing"; },
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

$data = json_decode(file_get_contents('php://input'), true);

// Ellenőrizzük, hogy minden adat megvan-e
foreach ($fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Hiányos kérés.');
        echo $result->toJSON();
        exit();
    }
    
    $values[$field] = $data[$field];
}


// Validáljuk az elemeket
$validator = new InputValidator($values, $rules);
if ($validator->test()->isError()) {
    http_response_code(400);
    $result = new Result(Result::ERROR, "Hibás adat szerepel az űrlapban.");
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

// Adatok kiegészítése
$values["user_id"] = $user['id'];
$values["id"] = intval($values["id"]);
$values["zip"] = intval($values["zip"]);

$result = updateAutofill($values);

http_response_code($result->isError() ? 400 : 200 );
if ($result->isSuccess()) {
    echo getAutofillFromId($values["id"], $values["type"])->messageJSON();
}
else if ($result->isOfType(Result::NO_AFFECT)) {
    echo (new Result(Result::NO_AFFECT, "Nem történt változás."))->toJSON();
}
else {
    echo $result->toJSON();
}
exit();