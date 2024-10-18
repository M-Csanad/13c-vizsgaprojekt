<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egyszerű regisztráció</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="./css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <form action="" method="post">
        <div class="form-header">
            <p>
                <a href="./" class="form-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                    Vissza a főoldalra
                </a>
            </p>
            <h1>Fiók létrehozása</h1>
        </div>
        <div class="input-wrapper">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required placeholder="">
            </div>
            <div class="input-group">
                <label for="username">Felhasználónév</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group-inline">
                <div class="input-group">
                    <label for="passwd">Jelszó</label>
                    <input type="password" name="passwd" id="passwd" required>
                </div>
                <div class="input-group">
                    <label for="passwd2">Jelszó megerősítése</label>
                    <input type="password" name="passwd2" id="passwd2" required>
                </div>
            </div>
            <div class="input-group-inline">
                <input type="checkbox" name="agree" id="agree" required>
                <label for="agree">Regisztrációmmal elfogadom az <a href="">ÁSZF-et.</a></label>
            </div>
            <div class="center">
                <div class="g-recaptcha" data-sitekey="6LeX3lUqAAAAAIE9E4-N_nbdUNW9BuIbLaI4nJ2v"></div>
            </div>
        </div>
        <input type="submit" value="Profil létrehozása" name="register" class="action-button">
    </form>
</body>
</html>
<?php
    include "./auth/login_register_functions.php";

    if (isset($_POST['register'])) {
        $recaptcha_secret = '6LeX3lUqAAAAADc1EcUrbOb9k_dbElHOgfwQ-lqg';
        $recaptcha_response = $_POST['g-recaptcha-response'];

        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
        $response_keys = json_decode($response, true);

        if (intval($response_keys["success"]) != 1) {
            echo "Kérjük töltse ki a reCAPTCHA ellenőrzést.";
        } 
        else {
            echo "reCAPTCHA megerősíve. Űrlap feldolgozva.<br>";
            $username = $_POST['username'];
            $password = $_POST['passwd'];
            $email = $_POST['email'];

            $result = register($username, $password, $email);
            echo $result;
        }
    }

?>