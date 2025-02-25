<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require      __DIR__.'/../../vendor/autoload.php';
include_once __DIR__.'/result.php';
include_once __DIR__.'/../env_config.php';

/**
 * Mail osztály
 * 
 * A Mail osztály felelős az email küldéséért a PHPMailer könyvtár segítségével.
 * Támogatja az aszinkron és szinkron email küldést, valamint különböző típusú email sablonokat.
 */
class Mail {
    private $mailer;
    private $username;
    private $password;
    private $name;

    /**
     * Mail konstruktor.
     * 
     * Inicializálja a PHPMailer objektumot és betölti a környezeti változókat.
     */
    public function __construct() {
        $this->mailer = new PHPMailer(true);

        load_env(__DIR__ . '/../.env');
        $this->username = $_ENV['EMAIL_USERNAME'];
        $this->password = $_ENV['EMAIL_PASSWORD'];
        $this->name = $_ENV['EMAIL_NAME'];
    }

    /**
     * Email küldése a megadott címzetteknek.
     * 
     * @param array $recipients A címzettek listája.
     * @param array $mail Az email tartalma (tárgy, törzs, stb.).
     * @param bool $async Ha true, az email aszinkron módon kerül küldésre.
     * @return Result Egy Result objektum, amely tartalmazza a küldés eredményét.
     */
    public function sendTo(array $recipients, array $mail, bool $async = false): Result {
        // Aszinkron küldés egy új háttérfolyamat indításával
        if ($async) {
            $dir = dirname(realpath(__FILE__));
            $recipientBase64 = base64_encode(json_encode($recipients, JSON_UNESCAPED_UNICODE));
            $mailBase64 = base64_encode(json_encode($mail, JSON_UNESCAPED_UNICODE));

            // PATH ellenőrzés és javítás
            $phpDir = "C:\\xampp\\php";
            $currentPath = getenv('PATH');

            if (strpos($currentPath, $phpDir) === false) {
                putenv('PATH=' . $currentPath . ';' . $phpDir);
            }

            // Parancs elkészítése Windowsra és Linuxra
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command = "start /B php $dir/mail_async_worker.php $recipientBase64 $mailBase64 > NUL 2>&1";
            } else {
                $command = "php $dir/mail_async_worker.php $recipientBase64 $mailBase64 > /dev/null 2>&1 &";
            }

            // Háttérfolyamat indítása
            pclose(popen($command, "r"));
            return new Result(Result::SUCCESS, 'Email küldés aszinkron módban.');
        }

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host       = 'smtp.gmail.com';
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $this->username;
            $this->mailer->Password   = $this->password;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = 587;
        
            $this->mailer->setFrom($this->username, $this->name);

            if (isset($recipients[0]) && is_array($recipients[0])) {
                foreach ($recipients as $recipient) {
                    $this->mailer->addAddress($recipient["email"], $recipient["name"]);
                }
            } else {
                $this->mailer->addAddress($recipients["email"], $recipients["name"]);
            }
            
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'utf-8'; 
            $this->mailer->Subject = $mail["subject"];
            $this->mailer->Body    = $mail["body"];
            $this->mailer->AltBody = $mail["alt"];
        
            $this->mailer->send();
            return (new Result(Result::SUCCESS, 'Az üzenet sikeresen elküldve.'));
        } catch (Exception $e) {
            return (new Result(Result::ERROR, 'Sikertelen üzenetküldés: '. $this->mailer->ErrorInfo));
        }
    }

    /**
     * Email törzsének generálása a megadott típus alapján.
     * 
     * @param string $type Az email típusa (pl. 'order', 'reset_password').
     * @param string $name A címzett neve.
     * @param array $data Az emailhez szükséges adatok.
     * @return string Az email törzse HTML formátumban.
     */
    public static function getMailBody(string $type='order', string $name, array $data): string {
        switch ($type) {
            case 'order':
                $orderNumber = $data['orderNumber'];
                $orderDate = $data['orderDate'];
                $orderTotal = $data['orderTotal'];
                $items = $data['items'];
    
                $mailBody = "
                <html>
                <head>
                    <style>
                        /* Responsive email styles */
                        @media only screen and (max-width: 600px) {
                            .content {
                                padding: 10px !important;
                            }
                            .email-header h1 {
                                font-size: 22px !important;
                            }
                            .content p, .content ul {
                                font-size: 14px !important;
                            }
                        }
                    </style>
                </head>
                <body style='margin: 0; padding: 0; background-color: #ffffff; font-family: Arial, sans-serif;'>
                    <div>
                    <div style='text-align: center; padding: 20px 0; background-color: #f8f8f8;'>
                        <h1 style='margin: 0; color: #333333;'>Köszönjük a rendelését!</h1>
                        <p style='color: #9acd32; font-weight: bold;'>A rendelése sikeresen rögzítésre került.</p>
                    </div>
                    
                    <div style='padding: 20px; background-color: #ffffff; text-align: left;'>
                        <p>Kedves <strong style='color: #333333;'>{$name}</strong>,</p>
                        <p style='color: #333333;'>Köszönjük, hogy nálunk vásárolt! A rendelése (<strong style='color: #333333;'>#{$orderNumber}</strong>) feldolgozás alatt áll.</p>
                        <p style='color: #333333;'>A rendelés dátuma: <strong style='color: #333333;'>{$orderDate}</strong></p>
                        <p style='color: #333333;'>A teljes összeg: <strong style='color: #333333;'>{$orderTotal} Ft</strong></p>
                        <p style='color: #333333;'>Rendelésed tartalma:</p>
                        <ul style='color: #333333; padding-left: 20px;'>
                ";
    
                foreach ($items as $item) {
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    $mailBody .= "<li>{$item['name']} - {$item['quantity']} db - {$subtotal} Ft</li>";
                }
    
                $mailBody .= "
                        </ul>
                        <p> Szállítási díj: 1000 Ft </p>
                        <p style='color: #333333;'>Ha bármilyen kérdése van, bátran <a href='mailto:florensbotanica@gmail.com' style='color: #9acd32;'>lépjen kapcsolatba velünk</a>.</p>
                    </div>
                    
                    <div style='background-color: #1d1d1d; text-align: center; padding: 20px 0;'>
                        <p style='color: #f5f5f5;'>© 2025 Florens Botanica. Minden jog fenntartva.</p>
                    </div>
                    </div>
                </body>
                </html>";
    
                return $mailBody;
    
            case "reset_password":
                $token = $data["token"];
                $resetLink = "http://localhost/reset?token={$token}";
                
                $mailBody = "
                <html>
                <head>
                    <style>
                        @media only screen and (max-width: 600px) {
                            .content {
                                padding: 10px !important;
                            }
                            .email-header h1 {
                                font-size: 22px !important;
                            }
                        }
                    </style>
                </head>
                <body style='margin: 0; padding: 0; background-color: #ffffff; font-family: Arial, sans-serif;'>
                    <div>
                        <div style='text-align: center; padding: 20px 0; background-color: #f8f8f8;'>
                            <h1 style='margin: 0; color: #333333;'>Jelszó visszaállítás</h1>
                        </div>
                        
                        <div style='padding: 20px; background-color: #ffffff; text-align: left;'>
                            <p>Kedves <strong style='color: #333333;'>{$name}</strong>,</p>
                            <p style='color: #333333;'>Jelszó visszaállítást kért a fiókjához. Kattintson az alábbi gombra a jelszó módosításához:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' style='background-color: #9acd32; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Jelszó v[...]
                            </div>
                            <p style='color: #333333;'>Ha nem Ön kérte a jelszó visszaállítást, kérjük hagyja figyelmen kívül ezt az emailt.</p>
                            <p style='color: #333333; font-size: 12px;'>A link 30 percig érvényes.</p>
                        </div>
                        
                        <div style='background-color: #1d1d1d; text-align: center; padding: 20px 0;'>
                            <p style='color: #f5f5f5;'>© 2025 Florens Botanica. Minden jog fenntartva.</p>
                        </div>
                    </div>
                </body>
                </html>";

                return $mailBody;

            default:
                break;
        }
    }       

    /**
     * Az email alternatív törzsének generálása a megadott típus alapján.
     * 
     * @param string $type Az email típusa (pl. 'order', 'reset_password').
     * @param string $name A címzett neve.
     * @param array $data Az emailhez szükséges adatok.
     * @return string Az email alternatív törzse egyszerű szöveg formátumban.
     */
    public static function getMailAltBody(string $type="order", string $name, array $data) {
        switch ($type) {
            case 'order':
                $orderNumber = $data['orderNumber'];
                $orderDate = $data['orderDate'];
                $orderTotal = $data['orderTotal'];
                $items = $data['items'];
    
                $altBody = "Kedves {$name},\n\n";
                $altBody .= "Köszönjük, hogy nálunk vásárolt! A rendelése (#{$orderNumber}) feldolgozás alatt áll.\n\n";
                $altBody .= "A rendelés dátuma: {$orderDate}\n";
                $altBody .= "A teljes összeg: {$orderTotal} Ft\n\n";
                $altBody .= "Rendelésed tartalma:\n";
                
                foreach ($items as $item) {
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    $altBody .= "<li>{$item['name']} - {$item['quantity']} db - {$subtotal} Ft</li>";
                }

                $altBody .= "Szállítási díj: 1000 Ft";
                $altBody .= "\nHa bármilyen kérdése van, bátran lépjen kapcsolatba velünk a florensbotanica@gmail.com email címen.\n";
    
                return $altBody;
    
            case "reset_password":
                $token = $data["token"];
                $resetLink = "http://localhost:3000/reset-password?token={$token}";
                
                $altBody = "Kedves {$name},\n\n";
                $altBody .= "Jelszó visszaállítást kért a fiókjához.\n\n";
                $altBody .= "A jelszó visszaállításához kattintson az alábbi linkre vagy másolja böngészőjébe:\n";
                $altBody .= "{$resetLink}\n\n";
                $altBody .= "Ha nem Ön kérte a jelszó visszaállítást, kérjük hagyja figyelmen kívül ezt az emailt.\n\n";
                $altBody .= "A link 30 percig érvényes.\n\n";
                $altBody .= "Üdvözlettel,\nFlorens Botanica webáruház";

                return $altBody;

            default:
                break;
        }
    }        
}