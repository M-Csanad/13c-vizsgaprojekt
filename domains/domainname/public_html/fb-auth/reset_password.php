<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once "../../../../.ext/init.php";

$hasToken = isset($_GET['token']);
$isValidToken = false;

if ($hasToken) {
    $result = selectData(
        "SELECT id FROM user WHERE reset_token = ? AND reset_token_expires_at > ?",
        [$_GET['token'], time()],
        "si"
    );
    $isValidToken = !$result->isEmpty();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Florens Botanica - Jelszó visszaállítása</title>

    <link rel="preload" href="/fb-auth/assets/fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="/fb-auth/assets/css/root.css">
    <link rel="stylesheet" href="/fb-auth/assets/css/register.css">
    <link rel="stylesheet" href="/fb-auth/assets/css/inputs-light.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="stylesheet" href="/fb-content/assets/css/font.css" />

    <script async defer src="https://www.google.com/recaptcha/enterprise.js?render=6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script type="module" defer src="/fb-auth/assets/js/reset_password.js"></script>
</head>
<body>
<div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div></div>
    <div class="main">
        <div class="side-image">
            <div class="bg visible" style="background-image: url('./fb-content/assets/media/images/site/login/bg0.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg1.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg2.jpg');"></div>
            <div class="bg" style="background-image: url('./fb-content/assets/media/images/site/login/bg3.jpg');"></div>
        </div>
        <form id="resetPassword" data-type="<?php echo $hasToken && $isValidToken ? 'reset' : 'request'; ?>" class="light">
            <div class="form-header">
                <h1><?php echo $hasToken && $isValidToken ? 'Új jelszó megadása' : 'Jelszó visszaállítása'; ?></h1>
                <div><?php echo $hasToken && $isValidToken ? 'Kérjük adja meg az új jelszavát.' : 'Adja meg az email címét a jelszó visszaállításához.'; ?></div>
            </div>
            <div class="form-body">
                <div class="input-wrapper">
                    <?php if (!$hasToken || !$isValidToken): ?>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" required placeholder="" autocomplete="email" tabindex="1">
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="password">Új jelszó</label>
                            <input type="password" name="password" id="password" required placeholder="" autocomplete="new-password" tabindex="1">
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <div class="input-group" tabindex="-1">
                        <div class="input-body" tabindex="-1">
                            <label for="password_confirm">Jelszó megerősítése</label>
                            <input type="password" name="password_confirm" id="password_confirm" required placeholder="" autocomplete="new-password" tabindex="1">
                        </div>
                        <div class="message-wrapper">
                            <div class="error-message"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                </div>
                <div class="form-bottom">
                    <div class="form-message"></div>
                    <div class="loader hidden"></div>
                    <input type="button" class="action-button" value="<?php echo $hasToken && $isValidToken ? 'Jelszó módosítása' : 'Link küldése'; ?>">
                    <div class="login">Vissza a <a href="./login" class="form-link">bejelentkezéshez</a></div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
