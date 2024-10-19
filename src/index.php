<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - TESZT</title>
    <?php
    // PHP session indítása
    session_start();
    $isLoggedIn = false;  // Alapértelmezett, hogy a felhasználó nincs bejelentkezve
    
    // Emlékezz rám funkció - ellenőrzi, hogy van-e 'rememberMe' süti
    if (isset($_COOKIE['rememberMe'])) {
        // Az adatbázis csatlakozási fájl és a süti kezeléséhez szükséges funkciók betöltése
        include "./auth/db_connect.php";
        include "./auth/cookie_session_functions.php";

        $cookieToken = $_COOKIE['rememberMe']; // A süti token
    
        // SQL lekérdezés előkészítése, ami ellenőrzi, hogy létezik-e a süti a felhasználóhoz
        $loginStatement = $db->prepare("SELECT COUNT(*) as num, user.password_hash, user.role, user.id, user.user_name, user.cookie_expires_at FROM user WHERE user.cookie_id = ?");
        $loginStatement->bind_param("i", $cookieToken); // Token hozzárendelése a lekérdezéshez
    
        $successfulLogin = $loginStatement->execute(); // Lekérdezés végrehajtása
    
        // Ha sikeres a lekérdezés
        if ($successfulLogin) {
            $result = $loginStatement->get_result(); // Eredmény lekérése
            $user = $result->fetch_assoc(); // Felhasználói adatok lekérése
    
            // Ellenőrzi, hogy a süti lejárati ideje nem múlt-e el
            if (time() < $user['cookie_expires_at']) {
                // Beállítja a session adatokat, ha a süti még érvényes
                setSessionData($user);
            }
        } else {
            // Hiba dobása, ha nem sikerül a lekérdezés
            throw new Exception($loginStatement->error);
        }
    }

    // Ha a felhasználó már be van jelentkezve (session alapján)
    if (isset($_SESSION['user_name'])) {
        $sessionId = session_id(); // Session ID lekérése
        $isLoggedIn = true;  // Bejelentkezett felhasználó beállítása
    }
    ?>
</head>

<body>
    <header>
        <h1>Főoldal</h1>
    </header>
    <p>
        <?php
        // Ha be van jelentkezve a felhasználó, megjelenik a kijelentkezési link
        if ($isLoggedIn) {
            echo "<a href='./logout'>Kijelentkezés</a>";
        }
        // Ha nincs bejelentkezve, megjelenik a bejelentkezési link
        else {
            echo "<a href='./login'>Bejelentkezés</a>";
        }
        ?>
    </p>
    <p><a href="./register">Regisztráció</a></p> <!-- Regisztrációs link -->
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates ipsa aperiam excepturi pariatur, sapiente
        dolorem, quis iure ratione veritatis autem fugit eius tempore. Minus hic suscipit omnis explicabo illo nulla.
    </p>
</body>

</html>

<?php
// Ha a felhasználó be van jelentkezve, üdvözlő üzenet jelenik meg a session adatokkal
if ($isLoggedIn) {
    echo "Üdvözlünk, {$_SESSION['user_name']}! {$_SESSION['role']} jogosultságod van."; // Felhasználónév és jogosultság kiírása
    echo "A sessionID-d $sessionId"; // Session ID kiírása
}

// Ha a felhasználó sikeresen kijelentkezett, egy zöld üzenet jelenik meg
if (isset($_SESSION['successful_logout']) && $_SESSION['successful_logout']) {
    echo "<p style='color: green;'>Sikeres kijelentkezés.</p>"; // Sikeres kijelentkezési üzenet
    unset($_SESSION['successful_logout']); // A sikeres kijelentkezési üzenet eltávolítása a session-ből
}
?>

