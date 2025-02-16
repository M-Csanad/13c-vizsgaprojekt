<?php
include_once __DIR__.'/../init.php';

// Új segédfüggvény a válasz küldéséhez
function sendResponse($result, $httpCode = null) {
    if (!$httpCode) {
        $httpCode = $result->isSuccess() ? 200 : 400;
    }
    http_response_code($httpCode);
    echo $result->toJSON();
    exit();
}

// Metódus ellenőrzése (guard clause)
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendResponse(new Result(Result::ERROR, 'Hibás metódus! Várt: PUT'), 405);
}

// JSON adatok megszerzése
$data = json_decode(file_get_contents('php://input'), true);

// Ellenőrizzük, hogy minden adat megvan-e
$requiredFields = ['type', 'data'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        sendResponse(new Result(Result::ERROR, 'Kérlek töltsd ki mindegyik mezőt!'), 400);
    }
}

// Felhasználó adatainak lekérése
$result = getUserData();
if (!$result->isSuccess()) {
    sendResponse(new Result(Result::DENIED, "Csak bejelentkezett felhasználó indíthat kéréseket!"), 401);
}
$user = $result->message[0];

$rules = [
    "first_name" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
    "last_name"  => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
    "phone"      => '/^(\+36|06)(\d{9})$/',
];

$result = new Result(Result::ERROR, "Hibás adat.");

switch ($data['type']) {
    case 'avatar':
        $id = $data["data"]["avatar_id"] ?? null;
        if (is_int($id)) {
            $update = updatePersonalDetails("avatar_id", $id, $user['id'], 'ii');
            if (!$update->isError()) {
                $result = getProfileUri($user["id"]);
            }
        }
        break;
    
    case 'first_name':
        $firstName = $data["data"] ?? null;
        if ($firstName && preg_match($rules["first_name"], $firstName)) {
            $update = updatePersonalDetails("first_name", $firstName, $user['id'], 'si');
            if (!$update->isError()) {
                $result = new Result(Result::SUCCESS, $firstName);
            }
        }
        break;
    
    case 'last_name':
        $lastName = $data["data"] ?? null;
        if ($lastName && preg_match($rules["last_name"], $lastName)) {
            $update = updatePersonalDetails("last_name", $lastName, $user['id'], 'si');
            if (!$update->isError()) {
                $result = new Result(Result::SUCCESS, $lastName);
            }
        }
        break;
    
    case 'email':
        $email = $data["data"] ?? null;
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $update = updatePersonalDetails("email", $email, $user['id'], 'si');
            if (!$update->isError()) {
                $result = new Result(Result::SUCCESS, $email);
            }
        }
        break;
    
    case 'phone':
        $phone = $data["data"] ?? null;
        if ($phone && preg_match($rules["phone"], $phone)) {
            $update = updatePersonalDetails("phone", $phone, $user['id'], 'si');
            if (!$update->isError()) {
                $result = new Result(Result::SUCCESS, $phone);
            }
        }
        break;
    
    default:
        // Ismeretlen módosítási típus esetén
        break;
}

sendResponse($result);