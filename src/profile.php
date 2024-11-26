<?php 
    include_once "./auth/init.php";
    session_start(); 

    if (isset($_SESSION['user_name'])) {

        $result = getUserData($_SESSION['user_id']);
        
        if (typeOf($result, "ERROR")) {
            echo "<div class='error'>", $result["message"], "</div>";
            exit();
        }
        else if (typeOf($result, "EMPTY")) { // Ha nincs olyan user, akinek az id-ja megegyezik a SESSION-ben lévővel
            header("Location: ./");
            exit();
        }

        $user = $result["message"];
    } 
    else {
        header("Location: ./");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beállítások</title>

    <link rel="preload" href="fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/root.css">
    <link rel="stylesheet" href="./css/profile.css">
    <link rel="shortcut icon" href="./web/media/img/herbalLogo_mini_white.png" type="image/x-icon">

    <script src="./js/profile.js"></script>
</head>
<body>
    <div class="main">
        <div class="sidebar-wrapper dynamic-border" style="--mouse-x: 50%; --mouse-y: 50%; --radius:0px">
            <div class="sidebar">
                <div class="profile-general">
                    <div class="profile-picture">
                        <img src="<?= $user['pfp_uri'] ?>" alt="Profilkép" width="130" height="130">
                    </div>
                    <div class="name">
                        <?= $user['last_name']." ".$user['first_name'] ?>
                    </div>
                    <div class="username">
                        <?= "@".$user['user_name'] ?>
                    </div>
                </div>
                <div class="pages">
                    <div class="pages-header">Beállítások</div>
                    <div class="page active" data-pageid="0" tabindex="0">
                        Személyes adatok
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="1" tabindex="0">
                        Automatikus kitöltés
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="2" tabindex="0">
                        Rendeléseim
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="3" tabindex="0">
                        Jelszó
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                        </svg>
                    </div>
                </div>
                <div class="sidebar-bottom">
                    <div class="logout">
                        Kijelentkezés
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrapper dynamic-border"  style="--mouse-x: 50%; --mouse-y: 50%; --radius: 0px">
            <div class="content">
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Enim mollitia incidunt harum vero ullam illo laboriosam magni, iure iste repellat laudantium, eligendi vitae blanditiis dicta molestias provident nisi voluptates eveniet?
            </div>
        </div>
    </div>
</body>
</html>