<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egyszerű regisztráció</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <p><a href="./">Vissza a főoldalra</a></p>
    <form action="" method="post">
        <label for="username">Felhasználónév: </label>
        <input type="text" name="username" id="username">
        <label for="passwd">Jelszó: </label>
        <input type="password" name="passwd" id="passwd">
        <label for="email">E-mail: </label>
        <input type="email" name="email" id="email">
        <div class="g-recaptcha" data-sitekey="6LeX3lUqAAAAAIE9E4-N_nbdUNW9BuIbLaI4nJ2v"></div>
        <input type="submit" value="Profil létrehozása" name="register">
    </form>
</body>
</html>
<?php
    include "./login_register_functions.php";
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