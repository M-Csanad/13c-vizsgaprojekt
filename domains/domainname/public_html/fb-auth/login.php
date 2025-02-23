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

load_env(__DIR__ . '/../../../../.ext/.env');
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Florens Botanica - Bejelentkezés</title>

    <link rel="preload" href="./fb-auth/assets/fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./fb-auth/assets/css/root.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/inputs-light.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/login.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="stylesheet" href="/fb-content/assets/css/font.css" />

    <script src="https://www.google.com/recaptcha/enterprise.js?render=<?php echo $_ENV['RECAPTCHA_SITE_KEY']; ?>"></script>
    <script src="./fb-auth/assets/js/prevent-resubmit.js" defer></script>

    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script type="module" defer src="./fb-auth/assets/js/login.js"></script>
</head>

<body>
<div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div></div>
    <div class="main">
        <div class="side-image">
            <div class="bg visible"
                style="background-image: url('./fb-content/assets/media/images/site/login/bg0.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg1.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg2.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg3.jpg');"></div>
        </div>
        <form method="post" id="login" class="light">
            <div class="form-header">
                <h1>Üdvözöljük!</h1>
                <div>Adja meg az e-mail címét és jelszavát.</div>
            </div>
            <div class="form-body">
                <div class="input-wrapper">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="username">Felhasználónév</label>
                            <input type="text" name="username" id="username" autocomplete="username" required placeholder="" tabindex="1">
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="passwd">Jelszó</label>
                            <input type="password" name="passwd" id="passwd" autocomplete="current-password" required placeholder="" tabindex="1">
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="rememberMe" id="rememberMe">
                        <label for="rememberMe">Maradjak bejelentkezve</label>
                        <a href="/reset" class="form-link" id="forgotPassword">Elfelejtette a jelszavát?</a>
                    </div>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                </div>
                <div class="form-bottom">
                    <div class="message-container">
                        <div class="loader hidden">
                            <div class="spinner"></div>
                        </div>
                        <div class="form-message"></div>
                    </div>
                    <input type="button" name="login" class="action-button g-recaptcha" value="Bejelentkezés">
                    <div class="register">Nincs még fiókja? <a href="./register" class="form-link">Regisztráljon!</a></div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>