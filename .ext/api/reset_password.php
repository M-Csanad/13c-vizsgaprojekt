<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../classes/inputvalidator.php';
include_once __DIR__.'/../classes/captcha.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validationRules = [
        "email" => [
            "rule" => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
            "message" => "Kérjük adjon meg egy érvényes email címet"
        ]
    ];

    $validator = new InputValidator($_POST, $validationRules);
    $validationResult = $validator->test();

    if ($validationResult->isError()) {
        http_response_code(400);
        echo $validationResult->toJSON();
        exit;
    }

    $captcha = new Captcha();
    $captchaResult = $captcha->verify($_POST['g-recaptcha-response'] ?? '', 'reset');
    
    if ($captchaResult->isError()) {
        http_response_code(401);
        echo $captchaResult->toJSON();
        exit;
    }

    $result = selectData(
        "SELECT id, email, first_name, last_name FROM user WHERE email = ?", 
        $_POST['email'], 
        "s"
    );

    if ($result->isEmpty()) {
        http_response_code(404);
        echo (new Result(Result::ERROR, "Nem található felhasználó ezzel az email címmel."))->toJSON();
        exit;
    }

    $user = $result->message[0];
    $resetResult = sendPasswordResetEmail($user);

    if ($resetResult->isSuccess()) {
        echo (new Result(Result::SUCCESS, "A jelszó visszaállítási link elküldve az email címére."))->toJSON();
    } else {
        http_response_code(500);
        echo $resetResult->toJSON();
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $validationRules = [
        "password" => [
            "rule" => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/',
            "message" => "A jelszó nem felel meg a követelményeknek"
        ],
        "password_confirm" => [
            "rule" => fn($value) => $value === $data['password'],
            "message" => "A két jelszó nem egyezik meg"
        ]
    ];

    $validator = new InputValidator($data, $validationRules);
    $validationResult = $validator->test();

    if ($validationResult->isError()) {
        http_response_code(400);
        echo $validationResult->toJSON();
        exit;
    }

    $captcha = new Captcha();
    $captchaResult = $captcha->verify($data['g-recaptcha-response'] ?? '', 'reset_confirm');
    
    if ($captchaResult->isError()) {
        http_response_code(401);
        echo $captchaResult->toJSON();
        exit;
    }

    $result = selectData(
        "SELECT id FROM user WHERE reset_token = ? AND reset_token_expires_at > ?",
        [$data['token'], time()],
        "si"
    );

    if ($result->isEmpty()) {
        http_response_code(400);
        echo (new Result(Result::ERROR, "Érvénytelen vagy lejárt token."))->toJSON();
        exit;
    }

    $userId = $result->message[0]['id'];
    $updateResult = resetUserPassword($userId, $data['password']);

    if ($updateResult->isSuccess()) {
        echo (new Result(Result::SUCCESS, "A jelszó sikeresen módosítva."))->toJSON();
    } else {
        http_response_code(500);
        echo (new Result(Result::ERROR, "Hiba történt a jelszó módosítása során."))->toJSON();
    }
    exit;
}

http_response_code(405);
echo (new Result(Result::ERROR, "Nem támogatott HTTP metódus."))->toJSON();
exit;