<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Florens Botanica - Bejelentkezés</title>
    
    <link rel="preload" href="fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/root.css">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="shortcut icon" href="./web/media/img/herbalLogo_mini_white.png" type="image/x-icon">

    <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj"></script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</head>

<body>

    <div class="main">
        <div class="side-image">
            <div class="bg visible" style="background-image: url('./images/site/bg0.jpg');"></div>
            <div class="bg" style="background-image: url('./images/site/bg1.jpg');"></div>
            <div class="bg" style="background-image: url('./images/site/bg2.jpg');"></div>
            <div class="bg" style="background-image: url('./images/site/bg3.jpg');"></div>
        </div>
        <form method="post" id="login">
            <div class="form-header">
                <h1>Üdvözöljük!</h1>
                <div>Adja meg az e-mail címét és jelszavát.</div>
            </div>
            <div class="form-body">
                <div class="input-wrapper">
                    <div class="input-group">
                        <label for="username">Felhasználónév</label>
                        <input type="text" class="empty" name="username" id="username" autocomplete="username" required placeholder="" oninput="validateUserNameInput()" value="<?= isset($_POST['username']) ? $_POST['username'] : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label for="passwd">Jelszó</label>
                        <input type="password" class="empty" name="passwd" id="passwd" autocomplete="current-password" required placeholder="">
                    </div>
                    <div class="input-group-inline">
                        <div>
                            <input type="checkbox" name="rememberMe" id="rememberMe" placeholder="">
                            <label for="rememberMe">Maradjak bejelentkezve</label>
                        </div>
                        <a href="" class="form-link" id="forgotPassword">Elfelejtette a jelszavát?</a>
                    </div>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                </div>
                <div class="form-bottom">
                    <div class="form-message">
                        <?php

                        include_once "./auth/init.php";
                        // Ha az űrlapot elküldték (post metódussal), a bejelentkezési logika fut le
                        if (isset($_POST['login'])) {
                            $recaptcha_secret = 'AIzaSyCcDQrUSOEaoHn4LhsfQiU7hpqgxzWIxe4';
                            $project_id = 'florens-botanica-1727886723149';
                            $url = "https://recaptchaenterprise.googleapis.com/v1/projects/$project_id/assessments?key=$recaptcha_secret";

                            $token = $_POST['g-recaptcha-response']; 
                            $user_action = 'login'; 

                            $data = [
                                "event" => [
                                    "token" => $token,
                                    "expectedAction" => $user_action,
                                    "siteKey" => "6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj"
                                ]
                            ];

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Content-Type: application/json'
                            ]);

                            $response = curl_exec($ch);
                            curl_close($ch);

                            $response_data = json_decode($response, true);

                            if (!isset($response_data['tokenProperties']['valid']) || !$response_data['tokenProperties']['valid']) {
                                echo "Hibás reCAPTCHA. Kérjük próbálja újra később.";
                                return;
                            }

                            if ($response_data['event']['expectedAction'] === $user_action && $response_data['riskAnalysis']['score'] >= 0.5) {
                                $username = $_POST['username']; // Felhasználónév lekérése
                                $password = $_POST['passwd']; // Jelszó lekérése
                                $rememberMe = isset($_POST['rememberMe']); // Emlékezz rám opció lekérése

                                // Bejelentkezési függvény meghívása, amely visszaadja a siker vagy hiba állapotát
                                $result = login($username, $password, $rememberMe);

                                if (typeOf($result, "SUCCESS")) {
                                    header('Location: ./');
                                }
                                else {
                                    echo "Hibás felhasználónév, vagy jelszó.";
                                }
                            }
                            else {
                                echo "reCAPTCHA ellenőrzés sikertelen. Kérjük próbálja újra.";
                            }
                        }
                        ?>
                    </div>
                    <input type="submit" value="Bejelentkezés" name="login" class="action-button g-recaptcha">
                    <div class="register">Nincs még fiókja? <a href="./register" class="form-link">Regisztráljon!</a></div>
                </div>
            </div>
        </form>
    </div>
    <script src="./js/login.js"></script>
</body>

</html>
