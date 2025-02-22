<?php

include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle forgot password request
    $data = $_POST;
    
    if (!isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Hiányzó email cím.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $result = selectData(
        "SELECT id, email, first_name, last_name FROM user WHERE email = ?", 
        $data['email'], 
        "s"
    );

    if ($result->isEmpty()) {
        http_response_code(404);
        echo json_encode(['error' => 'Nem található felhasználó ezzel az email címmel.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $user = $result->message[0];
    $resetResult = sendPasswordResetEmail($user);

    if ($resetResult->isSuccess()) {
        echo json_encode(['message' => 'A jelszó visszaállítási link elküldve az email címére.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        // echo json_encode(['error' => 'Hiba történt a jelszó visszaállítási email küldése során.'], JSON_UNESCAPED_UNICODE);
        echo $resetResult->toJSON(true);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Handle password reset
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['token']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Hiányzó token vagy jelszó.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $result = selectData(
        "SELECT id FROM user WHERE reset_token = ? AND reset_token_expires_at > ?",
        [$data['token'], time()],
        "si"
    );

    if ($result->isEmpty()) {
        http_response_code(400);
        echo json_encode(['error' => 'Érvénytelen vagy lejárt token.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $userId = $result->message[0]['id'];
    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    $updateResult = updateData(
        "UPDATE user SET password_hash = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?",
        [$passwordHash, $userId],
        "si"
    );

    if ($updateResult->isSuccess()) {
        echo json_encode(['message' => 'A jelszó sikeresen módosítva.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Hiba történt a jelszó módosítása során.'], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Nem támogatott HTTP metódus.'], JSON_UNESCAPED_UNICODE);
exit();