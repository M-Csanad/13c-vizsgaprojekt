<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - TESZT</title>
    <?php 
        session_start(); 
        $isLoggedIn = false;

        // Emlékezz rám funkció
        if (isset($_COOKIE['rememberMe'])) {
            include "./db_connect.php";
            include "./cookie_session_functions.php";

            $cookieToken = $_COOKIE['rememberMe'];

            $loginStatement = $db -> prepare("SELECT COUNT(*) as num, user.password_hash, user.role, user.id, user.user_name, user.cookie_expires_at FROM user WHERE user.cookie_id = ?");
            $loginStatement -> bind_param("i", $cookieToken);

            $successfulLogin = $loginStatement -> execute();

            if ($successfulLogin) {
                $result = $loginStatement -> get_result();
                $user = $result -> fetch_assoc();

                if (time() < $user['cookie_expires_at']) { // Azt előzi meg, hogy egy ellopott, de lejárt sütivel ne tudjunk belépni
                    setSessionData($user);
                }
            }
            else {
                throw $loginStatement -> error;
            }

        }

        // Ha be van jelentkezve a felhasználó
        if (isset($_SESSION['user_name'])) { 
            $sessionId = session_id();
            $isLoggedIn = true;
        }
    ?>
</head>
<body>
    <header><h1>Főoldal</h1></header>
    <p>
        <?php 
            if ($isLoggedIn) {
                echo "<a href='./logout'>Kijelentkezés</a>";
            }
            else {
                echo "<a href='./login'>Bejelentkezés</a>";
            }
        ?>
    </p>
    <p><a href="./register">Regisztráció</a></p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates ipsa aperiam excepturi pariatur, sapiente dolorem, quis iure ratione veritatis autem fugit eius tempore. Minus hic suscipit omnis explicabo illo nulla.</p>
</body>
</html>
<?php
    if ($isLoggedIn) {
        echo "Welcome, {$_SESSION['user_name']}! You have {$_SESSION['role']} role.";
        echo "Your sessionID is $sessionId";
    }

    if (isset($_SESSION['successful_logout']) && $_SESSION['successful_logout']) {
        echo "<p style='color: green;'>You have been successfully logged out.</p>";
        unset($_SESSION['successful_logout']);
    }
?>