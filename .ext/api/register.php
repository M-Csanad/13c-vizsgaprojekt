<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once __DIR__."/../init.php";

if (isset($_POST['register']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $message = "";

    $recaptcha_secret = 'AIzaSyCcDQrUSOEaoHn4LhsfQiU7hpqgxzWIxe4';
    $project_id = 'florens-botanica-1727886723149';
    $url = "https://recaptchaenterprise.googleapis.com/v1/projects/$project_id/assessments?key=$recaptcha_secret";

    $token = $_POST['g-recaptcha-response']; 
    $user_action = 'register'; 

    $data = [
        "event" => [
            "token" => $token,
            "expectedAction" => $user_action,
            "siteKey" => "6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj"
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);
    if (!isset($response_data['tokenProperties']['valid']) || !$response_data['tokenProperties']['valid']) {
        $message = "Hibás reCAPTCHA. Kérjük próbálja újra később.";
        header("Unauthorized", true, 401);
    }
    else if ($response_data['event']['expectedAction'] === $user_action && $response_data['riskAnalysis']['score'] >= 0.5) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];

        $result = register($username, $password, $email, $firstname, $lastname);

        if ($result->isSuccess()) {
            $message = "Sikeres regisztráció!";
        }
        else {
           $message = $result->message;
           header("Unauthorized", true, 401);
        }
    }
    else {
        $message = "reCAPTCHA ellenőrzés sikertelen. Kérjük próbálja újra.";
        header("Unauthorized", true, 401);
    }
    echo json_encode(["message" => $message], JSON_UNESCAPED_UNICODE);
}
