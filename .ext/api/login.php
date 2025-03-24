<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once __DIR__."/../init.php";
include_once __DIR__."/../classes/inputvalidator.php";
include_once __DIR__."/../classes/captcha.php";

if (isset($_POST['login'])) {
    $validationRules = [
        "username" => [
            "rule" => fn($value) => !empty($value),
            "message" => "Kérjük ne hagyja üresen a felhasználónév mezőt"
        ],
        "passwd" => [
            "rule" => fn($value) => !empty($value),
            "message" => "Kérjük ne hagyja üresen a jelszó mezőt"
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
    $captchaResult = $captcha->verify($_POST['g-recaptcha-response'] ?? '', 'login');

    if ($captchaResult->isError()) {
        http_response_code(401);
        echo $captchaResult->toJSON();
        exit;
    }

    // reCAPTCHA sikeres, elvégezzük a bejelentkezést
    $username = $_POST['username'];
    $password = $_POST['passwd'];
    $rememberMe = isset($_POST['rememberMe']);

    session_start();
    $result = login($username, $password, $rememberMe);

    if ($result->isSuccess()) {
        echo (new Result(Result::SUCCESS, "Sikeres bejelentkezés"))->toJSON();
    } else {
        http_response_code(401);
        echo (new Result(Result::ERROR, "Hibás felhasználónév, vagy jelszó."))->toJSON();
    }
}
