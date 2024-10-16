<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egyszerű bejelentkezés</title>
</head>
<body>
    <p><a href="./">Vissza a főoldalra</a></p>
    <form action="" method="post">
        <label for="username">Felhasználónév: </label>
        <input type="text" name="username" id="username" autocomplete='username'>
        <br>
        <label for="passwd">Jelszó: </label>
        <input type="password" name="passwd" id="passwd" autocomplete='current-password'>
        <br>
        <input type="checkbox" name="rememberMe" id="rememberMe">
        <label for="rememberMe">Maradjak bejelentkezve</label>
        <br>
        <input type="submit" value="Log in" name="login">
    </form>
</body>
</html>
<?php
    include "./login_register_functions.php";

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['passwd'];
        $rememberMe = isset($_POST['rememberMe']);

        $result = login($username, $password, $rememberMe);
        if ($result === true) {
            header('Location: index.php');
        }
        else {
            echo $result;
        }
    }

?>