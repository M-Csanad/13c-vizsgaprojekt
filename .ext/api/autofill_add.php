<?php
include_once __DIR__.'/../classes/inputvalidator.php';
include_once __DIR__.'/../autofill_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

function validateInputs() {

}

// Bemeneti értékek ellenőrzése
$data = json_decode(file_get_contents('php://input'), true);
$fields = ["type", "name", "zip", "city", "street_house"];
$values = [];
$rules = [
    "type" => function ($e) {return $e === "delivery" || $e === "billing"; },
    "name" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
    "zip" => '/^[1-9]{1}[0-9]{3}$/',
    "city" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
    "street_house" => '/^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+ [a-záéíóöőúüű]{2,} \d{1,}(?:\/[A-Z]+)?$/',
];

// Ellenőrizzük, hogy minden adat megvan-e
foreach ($fields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Hiányos kérés.');
        echo $result->toJSON();
        exit();
    }

    $values[$field] = $data[$field];
}

// Validáljuk az elemeket
$validator = new InputValidator($values, $rules);

if (!$validator->test()) {
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
    return new Result(Result::DENIED, "Csak bejelentkezett felhasználó indíthat kéréseket!");
}
$values["user_id"] = $user['id'];

// Adatbázisba feltöltés
$result = uploadAutofill($values);