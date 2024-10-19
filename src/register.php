<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egyszerű regisztráció</title>

    <!-- Google reCAPTCHA script betöltése -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- Külső CSS fájl és Google Fonts betöltése -->
    <link rel="stylesheet" href="./css/register.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Regisztrációs űrlap -->
    <form action="" method="post">
        <div class="form-header">
            <!-- Visszalépési link a főoldalra, SVG ikonnal -->
            <p>
                <a href="./" class="form-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor"
                        viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                    Vissza a főoldalra
                </a>
            </p>
            <!-- Űrlap címe -->
            <h1>Fiók létrehozása</h1>
        </div>

        <!-- Bemeneti mezők a regisztrációhoz -->
        <div class="input-wrapper">
            <!-- E-mail cím mező -->
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" required placeholder="" autocomplete="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>">
            </div>

            <!-- Felhasználónév mező -->
            <div class="input-group">
                <label for="username">Felhasználónév</label>
                <input type="text" name="username" id="username" required placeholder="" autocomplete="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ""; ?>" pattern="[\w]+">
            </div>

            <!-- Jelszó és jelszó megerősítés mezők -->
            <div class="input-group-inline">
                <div class="input-group">
                    <label for="password">Jelszó</label>
                    <input type="password" name="password" id="password" required oninput="validatePasswordInputs()" autocomplete="new-password" placeholder="">
                </div>
                <div class="input-group">
                    <label for="passwordConfirm">Jelszó megerősítése</label>
                    <input type="password" name="passwordConfirm" id="passwordConfirm" required oninput="validatePasswordInputs()" autocomplete="new-password" placeholder="">
                </div>
            </div>

            <!-- Általános szerződési feltételek elfogadása checkbox -->
            <div class="input-group-inline">
                <input type="checkbox" name="agree" id="agree" required>
                <label for="agree">Regisztrációmmal elfogadom az <a href="">ÁSZF-et.</a></label>
            </div>

            <!-- Google reCAPTCHA védelmi mező -->
            <div class="center">
                <div class="g-recaptcha" data-sitekey="6LeX3lUqAAAAAIE9E4-N_nbdUNW9BuIbLaI4nJ2v"></div>
            </div>
        </div>

        <!-- Regisztráció gomb -->
        <input type="submit" value="Profil létrehozása" name="register" class="action-button">

        <!-- Hibák vagy sikeres üzenetek megjelenítése -->
        <div class='form-message'>
            <?php
            // Regisztrációs funkciókat tartalmazó fájl beillesztése
            include "./auth/login_register_functions.php";

            // Ha a regisztrációs űrlapot elküldték (post metódussal), a regisztrációs logika fut le
            if (isset($_POST['register'])) {
                // reCAPTCHA ellenőrzés
                $recaptcha_secret = '6LeX3lUqAAAAADc1EcUrbOb9k_dbElHOgfwQ-lqg';
                $recaptcha_response = $_POST['g-recaptcha-response'];

                // reCAPTCHA válasz ellenőrzése az API segítségével
                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
                $response_keys = json_decode($response, true);

                // Ha a reCAPTCHA ellenőrzés nem sikeres, hibaüzenet
                if (intval($response_keys["success"]) != 1) {
                    echo "Kérjük töltse ki a reCAPTCHA ellenőrzést.";
                }
                // Ha a reCAPTCHA sikeres, regisztráció feldolgozása
                else {
                    // Felhasználónév, jelszó és e-mail cím lekérése a post adatokból
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $email = $_POST['email'];

                    // Regisztrációs függvény meghívása, amely elmenti az új felhasználót
                    $result = register($username, $password, $email);

                    if ($result === true) {
                        // Ha a regisztráció sikeres, átirányítás a bejelentkezési oldalra
                        header("Location: ./login");
                    }
                    else {
                        echo $result;
                    }
                }
            }
            ?>
        </div>
    </form>
    <script src="./js/register.js"></script>
</body>

</html>
