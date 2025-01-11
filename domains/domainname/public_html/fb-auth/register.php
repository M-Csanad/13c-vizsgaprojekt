<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once "../../../../.ext/init.php";

$isLoggedIn = false;
$result = getUserData();
if ($result->isSuccess()) {
    $user = $result->message[0];

    // Ha be van jelentkezve, akkor kijelentkeztetjük
    header("Location: ./logout");
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Florens Botanica - Regisztráció</title>

    <link rel="preload" href="./fb-auth/assets/fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./fb-auth/assets/css/root.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/register.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="shortcut icon" href="./fb-content/assets/media/images/logos/herbalLogo_mini_white.png" type="image/x-icon">

    <script async defer src="https://www.google.com/recaptcha/enterprise.js?render=6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer src="./fb-auth/assets/js/register.js"></script>
</head>
<body>
    <div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div><div class="layer layer-0"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-1"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-2"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-3"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div></div>
    <div class="main">
        <div class="side-image">
            <div class="bg visible" style="background-image: url('./fb-content/assets/media/images/site/login/bg0.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg1.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg2.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg3.jpg');"></div>
        </div>
        <form action="" method="post" id="register">
            <div class="form-header">
                <h1>Üdvözöljük!</h1>
                <div>Töltse ki az űrlapot a regisztráláshoz.</div>
            </div>
            <div class="form-body">
                <div class="input-wrapper">
                    <div class="input-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" required placeholder="" autocomplete="email" value="" class="empty">
                    </div>
                    <div class="input-group">
                        <label for="username">Felhasználónév</label>
                        <input type="text" name="username" id="username" required placeholder="" autocomplete="username" value="" class="empty">
                    </div>
                    <div class="input-group-inline">
                        <div class="input-group">
                            <label for="lastname">Vezetéknév</label>
                            <input type="text" name="lastname" id="lastname" required placeholder="" class="empty">
                        </div>
                        <div class="input-group">
                            <label for="firstname">Keresztnév</label>
                            <input type="text" name="firstname" id="firstname" required placeholder="" class="empty">
                        </div>
                    </div>
                    <div class="form-divider"></div>
                    <div class="input-group-inline">
                        <div class="input-group">
                            <label for="password">Jelszó</label>
                            <input type="password" name="password" id="password" required autocomplete="new-password" placeholder="" class="empty">
                        </div>
                        <div class="input-group">
                            <label for="passwordConfirm">Jelszó megerősítése</label>
                            <input type="password" name="passwordConfirm" id="passwordConfirm" required autocomplete="new-password" placeholder="" class="empty">
                        </div>
                    </div>
                    <div class="input-group-inline">
                        <div>
                            <input type="checkbox" name="agree" id="agree" required class="empty">
                            <label for="agree">Regisztrációmmal elfogadom az <a href="" class="form-link">ÁSZF-et.</a></label>
                        </div>
                    </div>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" class="empty">
                </div>
                <div class="form-bottom">
                    <div class='form-message'></div>
                    <input type="submit" name="register" class="action-button g-recaptcha" value="Profil létrehozása" class="empty">
                    <div class="login">Regisztrált már? <a href="./login" class="form-link">Jelentkezzen be!</a></div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
