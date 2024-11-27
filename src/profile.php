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
    <meta name="description" content="Profil beállítások">
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
                    <div class="page active" data-pageid="0" tabindex="0">
                        Személyes adatok
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                            <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="1" tabindex="0">
                        Automatikus kitöltés
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                            <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="2" tabindex="0">
                        Rendeléseim
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                        </svg>
                    </div>
                    <div class="page" data-pageid="3" tabindex="0">
                        Jelszó
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                            <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                        </svg>
                    </div>
                </div>
                <div class="sidebar-bottom">
                    <div class="logout">
                        Kijelentkezés
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
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