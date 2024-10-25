<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="./css/root.css">
    <?php 
        include_once "./auth/init.php";
        session_start(); 

        if (isset($_SESSION['user_name'])) {

            $user = getUserData($_SESSION['user_id']);
            
            if ($user == "Nincs találat!") { // Ha nincs olyan user, akinek az id-ja megegyezik a SESSION-ben lévővel
                header("Location: ./");
                exit();
            }
            else if (!is_array($user)) {
                echo "<div class='error'>", $user, "</div>";
                exit();
            }
        } 
        else {
            header("Location: ./");
            exit();
        }
    ?>
</head>
<body>
    <header>
        <h1>
        <?php
            echo "Üdvözlünk, {$_SESSION['user_name']}!";
        ?>
        </h1>
    </header>
</body>
</html>