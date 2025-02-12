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

echo json_encode($values);