<?php

include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

// Bemeneti értékek ellenőrzése
$fields = ["old-pass", "new-pass", "new-pass-confirm"];
$values = [];
$rules = [
    "*" => [
        "rule" => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/',
        "message" => "Hibás jelszó."
    ]
];

$data = json_decode(file_get_contents('php://input'), true);

// Ellenőrizzük, hogy minden adat megvan-e
foreach ($fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Kérlek töltsd ki mindegyik mezőt!');
        echo $result->toJSON();
        exit();
    }
    
    $values[$field] = $data[$field];
}

// Validáljuk az elemeket
$validator = new InputValidator($values, $rules);
if ($validator->test()->isError()) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Kérjük ellenőrizd, hogy a jelszavak a formátumnak megfelelőek-e!');
    echo $result->toJSON();
    exit();
}

if ($values["old-pass"] === $values["new-pass"]) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Az új jelszó nem lehet ugyanaz, mint a régi!');
    echo $result->toJSON();
    exit();
}

if ($values["new-pass"] != $values["new-pass-confirm"]) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'A megadott jelszavak nem egyeznek!');
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
    http_response_code(401);
    $result = new Result(Result::DENIED, "Csak bejelentkezett felhasználó indíthat kéréseket!");
    echo $result->toJSON();
    exit();
}

// Régi jelszó ellenőrzése
$result = getHashedPassword($user["id"]);
if (!$result->isSuccess()) {
    http_response_code(500);
    $result = new Result(Result::ERROR, "Ismeretlen hiba merült fel.");
    echo $result->toJSON();
    exit();
}
$currentPassword = $result->message[0]["password_hash"];

if (!password_verify($values["old-pass"], $currentPassword)) {
    http_response_code(403);
    $result = new Result(Result::ERROR, "Helytelen jelszó.");
    echo $result->toJSON();
    exit();
}

// Jelszó módosítása
$result = modifyPassword($user["id"], password_hash($values["new-pass"], PASSWORD_DEFAULT));
if ($result->isSuccess()) {
    echo (new Result(Result::SUCCESS, "Sikeres módosítás!"))->toJSON();
}
else {
    echo (new Result(Result::SUCCESS, "Sikertelen módosítás, kérlek próbáld meg újra később!"))->toJSON();
}