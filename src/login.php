<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egyszerű bejelentkezés</title>
    <link rel="stylesheet" href="./css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>
<body>
    <form action="" method="post">
        <div class="form-header">
            <p>
                <a href="./" class="form-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                    Vissza a főoldalra
                </a>
            </p>
            <h1>Bejelentkezés</h1>
        </div>
        <div class="input-wrapper">
            
            <div class="input-group">
                <label for="username">Felhasználónév</label>
                <input type="text" name="username" id="username" autocomplete='username' required>
            </div>
            <div class="input-group">
                <label for="passwd">Jelszó</label>
                <input type="password" name="passwd" id="passwd" autocomplete='current-password' required>
            </div>
            <div class="input-group-inline">
                <input type="checkbox" name="rememberMe" id="rememberMe">
                <label for="rememberMe">Maradjak bejelentkezve</label>
            </div>
        </div>
        <input type="submit" value="Bejelentkezés" name="login" class="action-button">

        <a href="" class="form-link" id="forgotPassword">Elfelejtette a jelszavát?</a>
        <div class="form-message">
            <?php
                include "./login_register_functions.php";
    
                if (isset($_POST['login'])) {
                    $username = $_POST['username'];
                    $password = $_POST['passwd'];
                    $rememberMe = isset($_POST['rememberMe']);
    
                    $result = login($username, $password, $rememberMe);
    
                    if ($result === true) {
                        header('Location: ./');
                    }
                    else {
                        echo $result;
                    }
                }
    
            ?>
        </div>
    </form>
</body>
</html>