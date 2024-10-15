<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - TESZT</title>
</head>
<body>
    <header><h1>Főoldal</h1></header>
    <p><a href="./login">Bejelentkezés</a></p>
    <p><a href="./register">Regisztráció</a></p>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates ipsa aperiam excepturi pariatur, sapiente dolorem, quis iure ratione veritatis autem fugit eius tempore. Minus hic suscipit omnis explicabo illo nulla.</p>
</body>
</html>
<?php

    session_start();

    if (isset($_SESSION['user_name'])) {
        echo "Welcome, {$_SESSION['user_name']}! You have {$_SESSION['role']} role.";
    }
    else {
        echo "You are not logged in.";
    }

?>