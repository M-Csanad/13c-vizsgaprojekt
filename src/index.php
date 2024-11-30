<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - TESZT</title>
    <link rel="stylesheet" href="./css/root.css">
    <?php
    include_once "./auth/init.php";
    session_start();
    $isLoggedIn = false;  // Alapértelmezett, hogy a felhasználó nincs bejelentkezve
    
    // Emlékezz rám funkció - ellenőrzi, hogy van-e 'rememberMe' süti
    if (isset($_COOKIE['rememberMe'])) {
        $cookieToken = $_COOKIE['rememberMe'];
        $result = selectData("SELECT COUNT(*) as num, 
                            user.password_hash, 
                            user.role, user.id, 
                            user.user_name, 
                            user.cookie_expires_at 
                            FROM user 
                            WHERE user.cookie_id = ?", $cookieToken);
        
        if (typeOf($result, "SUCCESS")) {
            $user = $result["message"];
            if (time() < $user['cookie_expires_at']) {
                setSessionData($user);
            }
        }
        else {
            echo "<div class='error'>", $result["message"], "</div>";
            exit();
        }
    }

    // Ha a felhasználó már be van jelentkezve
    if (isset($_SESSION['user_name'])) {
        $sessionId = session_id();
        $isLoggedIn = true;
    }

    ?>
</head>

<body>
    <header>
        <h1>Főoldal</h1>
    </header>
    <p>
        <?php
        // Link megjelenítése bejelentkezési státusz alapján
        if ($isLoggedIn) {
            echo "<a href='./logout'>Kijelentkezés</a>";
        }
        else {
            echo "<a href='./login'>Bejelentkezés</a>";
        }
        ?>
    </p>
    <p><a href="./register">Regisztráció</a></p>
    <p><a href="./settings">Profil</a></p>
    <p><a href="./dashboard">Vezérlőpult</a></p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates ipsa aperiam excepturi pariatur, sapiente
        dolorem, quis iure ratione veritatis autem fugit eius tempore. Minus hic suscipit omnis explicabo illo nulla.
    </p>
</body>

</html>

<?php
// Ha a felhasználó be van jelentkezve, üdvözlő üzenet jelenik meg a session adatokkal
if ($isLoggedIn) {
    echo "Üdvözlünk, {$_SESSION['user_name']}! {$_SESSION['role']} jogosultságod van.";
    echo "A sessionID-d $sessionId";
}

// Ha a felhasználó sikeresen kijelentkezett, egy zöld üzenet jelenik meg
if (isset($_SESSION['successful_logout']) && $_SESSION['successful_logout']) {
    echo "<p style='color: green;'>Sikeres kijelentkezés.</p>";
    unset($_SESSION['successful_logout']); // A sikeres kijelentkezési üzenet eltávolítása a session-ből
}
?>

