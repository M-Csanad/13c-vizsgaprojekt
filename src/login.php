<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Egyszerű bejelentkezés</title>

    <!-- Külső CSS fájlok és Google Fonts betöltése -->
    <link rel="stylesheet" href="./css/root.css">
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <!-- Bejelentkezési űrlap -->
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
            <h1>Bejelentkezés</h1>
        </div>

        <!-- Bemeneti mezők -->
        <div class="input-wrapper">
            <!-- Felhasználónév mező -->
            <div class="input-group">
                <label for="username">Felhasználónév</label>
                <input type="text" class="empty" name="username" id="username" autocomplete='username' required placeholder="" oninput="validateUserNameInput()" value="<?= isset($_POST['username']) ? $_POST['username'] : ''; ?>">
            </div>
            <!-- Jelszó mező -->
            <div class="input-group">
                <label for="passwd">Jelszó</label>
                <input type="password" class="empty" name="passwd" id="passwd" autocomplete='current-password' required placeholder="">
            </div>
            <!-- "Emlékezz rám" opció (maradjak bejelentkezve) -->
            <div class="input-group-inline">
                <input type="checkbox" name="rememberMe" id="rememberMe" placeholder="">
                <label for="rememberMe">Maradjak bejelentkezve</label>
            </div>
        </div>

        <!-- Bejelentkezési gomb -->
        <input type="submit" value="Bejelentkezés" name="login" class="action-button">

        <!-- Elfelejtett jelszó link -->
        <a href="" class="form-link" id="forgotPassword">Elfelejtette a jelszavát?</a>

        <!-- Hibák vagy sikeres üzenetek megjelenítése -->
        <div class="form-message">
            <?php
            // Bejelentkezési funkciókat tartalmazó fájl beillesztése
            include_once "./auth/init.php";

            // Ha az űrlapot elküldték (post metódussal), a bejelentkezési logika fut le
            if (isset($_POST['login'])) {
                $username = $_POST['username']; // Felhasználónév lekérése
                $password = $_POST['passwd']; // Jelszó lekérése
                $rememberMe = isset($_POST['rememberMe']); // Emlékezz rám opció lekérése
            
                // Bejelentkezési függvény meghívása, amely visszaadja a siker vagy hiba állapotát
                $result = login($username, $password, $rememberMe);
                
                // Ha a bejelentkezés sikeres, átirányítás a főoldalra
                if ($result === true) {
                    header('Location: ./');
                }
                // Ha nem sikeres, a hibaüzenet kiírása
                else {
                    echo $result;
                }
            }
            ?>
        </div>
    </form>
    <script src="./js/login.js"></script>
</body>

</html>
