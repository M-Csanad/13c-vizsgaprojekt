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
                <div>Adja meg az felhasználónevét és jelszavát.</div>
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
                            <div class="show-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye eye-show default" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye-fill eye-show hover" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye-slash eye-hide default" viewBox="0 0 16 16">
                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye-slash-fill eye-hide hover" viewBox="0 0 16 16">
                                    <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                                </svg>
                            </div>
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