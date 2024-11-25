<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Florens Botanica - Regisztráció</title>

    <link rel="preload" href="fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/root.css">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="shortcut icon" href="./web/media/img/herbalLogo_mini_white.png" type="image/x-icon">

    <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj"></script>
</head>
<body>
    <div class="main">
        <div class="side-image">
            <div class="bg visible" style="background-image: url('./images/site/bg0.jpg');"></div>
            <div class="bg" style="background-image: url('./images/site/bg1.jpg');"></div>
            <div class="bg" style="background-image: url('./images/site/bg2.jpg');"></div>
            <div class="bg" style="background-image: url('./images/site/bg3.jpg');"></div>
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
                        <input type="email" name="email" id="email" required placeholder="" autocomplete="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>" class="empty">
                    </div>
                    <div class="input-group">
                        <label for="username">Felhasználónév</label>
                        <input type="text" name="username" id="username" required placeholder="" autocomplete="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ""; ?>" class="empty">
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
                    <div class='form-message'>
                        <?php

                        include_once "./auth/init.php";

                        if (isset($_POST['register'])) {
                            
                            $recaptcha_secret = 'AIzaSyCcDQrUSOEaoHn4LhsfQiU7hpqgxzWIxe4';
                            $project_id = 'florens-botanica-1727886723149';
                            $url = "https://recaptchaenterprise.googleapis.com/v1/projects/$project_id/assessments?key=$recaptcha_secret";

                            $token = $_POST['g-recaptcha-response']; 
                            $user_action = 'register'; 

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
                                $username = $_POST['username'];
                                $password = $_POST['password'];
                                $email = $_POST['email'];
                                $firstname = $_POST['firstname'];
                                $lastname = $_POST['lastname'];
                                
                                $result = register($username, $password, $email, $firstname, $lastname);
                                if (typeOf($result, "SUCCESS")) {
                                    header("Location: ./login");
                                }
                                else {
                                    echo $result["message"];
                                }
                            }
                            else {
                                echo "reCAPTCHA ellenőrzés sikertelen. Kérjük próbálja újra.";
                            }
                        }
                        ?>
                    </div>
                    <input type="submit" name="register" class="action-button g-recaptcha" value="Profil létrehozása" class="empty">
                    <div class="login">Regisztrált már? <a href="./login" class="form-link">Jelentkezzen be!</a></div>
                </div>
            </div>
        </form>
    </div>
    <script src="./js/register.js"></script>
    <script src="./js/prevent-resubmit.js"></script>
</body>
</html>
