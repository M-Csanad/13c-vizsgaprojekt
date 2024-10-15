<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple login</title>
</head>
<body>
    <p><a href="./">Vissza a főoldalra</a></p>
    <form action="" method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username">
        <label for="passwd">Password: </label>
        <input type="password" name="passwd" id="passwd">
        <input type="submit" value="Log in" name="login">
    </form>
</body>
</html>
<?php
    include "./login_register_functions.php";

    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['passwd'];

        $result = login($username, $password);
        if ($result === true) {
            header('Location: index.php');
        }
        else {
            echo $result;
        }
    }

?>