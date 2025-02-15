<?php

include_once __DIR__.'/../init.php';

// Metódus ellenőrzés
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

// JSON adatok megszerzése
$data = json_decode(file_get_contents('php://input'), true);

// Ellenőrizzük, hogy minden adat megvan-e
$fields = ['type', 'data'];
$values = [];
foreach ($fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        $result = new Result(Result::ERROR, 'Kérlek töltsd ki mindegyik mezőt!');
        echo $result->toJSON();
        exit();
    }
    
    $values[$field] = $data[$field];
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

// Különböző módosítási típusok kezelése, validálása
$result = new Result(Result::ERROR, "Hibás adat.");
switch ($values['type']) {
    case 'avatar':
        $id = $values["data"]["avatar_id"];

        if (is_int($id)) {
            // Az avatár frissítése
            $update = updatePersonalDetails("avatar_id", $user['id'], $id, 'ii');
            if (!$update->isError()) {
                // Új adat lekérdezése
                $result = getProfileUri($user["id"]);
            }
        }
        break;
    
    default:
        # code...
        break;
}

echo $result->toJSON();