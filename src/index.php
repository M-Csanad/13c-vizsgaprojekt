<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - TESZT</title>
    <?php 
        session_start(); 
        $isLoggedIn = false;
        var_dump($_SESSION);
        if (isset($_SESSION['user_name'])) { // Ha be van jelentkezve a felhasználó
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