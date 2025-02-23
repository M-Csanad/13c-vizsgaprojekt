<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once __DIR__."/../init.php";
include_once __DIR__."/../classes/inputvalidator.php";
include_once __DIR__."/../classes/captcha.php";

if (isset($_POST['register']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $validationRules = [
        "username" => [
            "rule" => '/^[\w-]{3,20}$/',
            "message" => "A felhasználónév 3-20 karakter hosszú lehet és csak betűket, számokat és kötőjelet tartalmazhat"
        ],
        "email" => [
            "rule" => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
            "message" => "Kérjük adjon meg egy érvényes email címet"
        ],
        "firstname" => [
            "rule" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
            "message" => "Kérjük adja meg a keresztnevét"
        ],
        "lastname" => [
            "rule" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
            "message" => "Kérjük adja meg a vezetéknevét"
        ],
        "password" => [
            "rule" => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/',
            "message" => "A jelszó nem felel meg a követelményeknek"
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
    $captchaResult = $captcha->verify($_POST['g-recaptcha-response'] ?? '', 'register');
    
    if ($captchaResult->isError()) {
        http_response_code(401);
        echo $captchaResult->toJSON();
        exit;
    }

    // reCAPTCHA sikeres, folytatjuk a regisztrációt
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    $result = register($username, $password, $email, $firstname, $lastname);

    if ($result->isSuccess()) {
        echo (new Result(Result::SUCCESS, "Sikeres regisztráció!"))->toJSON();
    } else {
        http_response_code(401);
        echo $result->toJSON();
    }
}
