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
    private const TEMPLATE_PATH = __DIR__ . '/../mail/templates/';

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
        // Betöltjük a megfelelő sablont
        $templateFile = Mail::TEMPLATE_PATH . $type . '.html';
        
        // Ellenőrizzük, hogy létezik-e a sablon fájl
        if (!file_exists($templateFile)) {
            error_log("Email sablon nem található: " . $templateFile);
            return "Email sablon nem található: ".$templateFile;
        }
        
        // Betöltjük a sablon tartalmát
        $template = file_get_contents($templateFile);
        
        // Alapvető változók cseréje
        $template = str_replace('{NAME}', $name, $template);
        
        switch ($type) {
            case 'order':
                // Rendelés specifikus változók cseréje
                $template = str_replace('{ORDER_NUMBER}', $data['orderNumber'], $template);
                $template = str_replace('{ORDER_DATE}', $data['orderDate'], $template);
                $template = str_replace('{ORDER_TOTAL}', $data['orderTotal'], $template);
                
                // Termékek listájának generálása
                $itemsHtml = '';
                foreach ($data['items'] as $item) {
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    $itemsHtml .= "<li>{$item['name']} - {$item['quantity']} db - {$subtotal} Ft</li>";
                }
                $template = str_replace('{ORDER_ITEMS}', $itemsHtml, $template);
                break;
                
            case 'reset_password':
                // Jelszó visszaállítás specifikus változók cseréje
                $resetLink = "http://localhost/reset?token={$data['token']}";
                $template = str_replace('{RESET_LINK}', $resetLink, $template);
                break;

            case 'status_update':
                $statuses = ['Visszaigazolva', 'Feldolgozás alatt', 'Szállítás alatt', 'Teljesítve'];
                $icons = [
                    // Confirmation icon
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>',
                    // Processing icon
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>',
                    // Shipping icon
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>',
                    // Complete icon
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
                ];
                
                // Generate status steps HTML
                $stepsHtml = '';
                foreach ($statuses as $index => $status) {
                    $isActive = $index <= $data['statusIndex'] ? 'active' : '';
                    $stepsHtml .= "
                        <div class='status-step'>
                            <div class='status-icon $isActive'>
                                {$icons[$index]}
                            </div>
                            <div class='status-label $isActive'>$status</div>
                        </div>";
                }
                
                // Calculate progress width (0-100%)
                $progressWidth = ($data['statusIndex'] / (count($statuses) - 1)) * 100;
                
                // Replace placeholders
                $template = str_replace('{ORDER_NUMBER}', $data['orderNumber'], $template);
                $template = str_replace('{ORDER_DATE}', $data['orderDate'], $template);
                $template = str_replace('{ORDER_TOTAL}', $data['orderTotal'], $template);
                $template = str_replace('{NEW_STATUS}', $data['newStatus'], $template);
                $template = str_replace('{STATUS_STEPS}', $stepsHtml, $template);
                $template = str_replace('{PROGRESS_WIDTH}', $progressWidth, $template);
                break;
        }
        
        return $template;
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
        // Betöltjük a megfelelő szöveges sablont
        $templateFile = Mail::TEMPLATE_PATH . $type . '.txt';
        
        // Ellenőrizzük, hogy létezik-e a sablon fájl
        if (!file_exists($templateFile)) {
            error_log("Email szöveges sablon nem található: " . $templateFile);
            return "Email szöveges sablon nem található.";
        }
        
        // Betöltjük a sablon tartalmát
        $template = file_get_contents($templateFile);
        
        // Alapvető változók cseréje
        $template = str_replace('{NAME}', $name, $template);
        
        switch ($type) {
            case 'order':
                // Rendelés specifikus változók cseréje
                $template = str_replace('{ORDER_NUMBER}', $data['orderNumber'], $template);
                $template = str_replace('{ORDER_DATE}', $data['orderDate'], $template);
                $template = str_replace('{ORDER_TOTAL}', $data['orderTotal'], $template);
                
                // Termékek listájának generálása
                $itemsText = '';
                foreach ($data['items'] as $item) {
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    $itemsText .= "- {$item['name']} - {$item['quantity']} db - {$subtotal} Ft\n";
                }
                $template = str_replace('{ORDER_ITEMS}', $itemsText, $template);
                break;
                
            case 'reset_password':
                // Jelszó visszaállítás specifikus változók cseréje
                $resetLink = "http://localhost/reset?token={$data['token']}";
                $template = str_replace('{RESET_LINK}', $resetLink, $template);
                break;

            case 'status_update':
                $template = str_replace('{ORDER_NUMBER}', $data['orderNumber'], $template);
                $template = str_replace('{ORDER_DATE}', $data['orderDate'], $template);
                $template = str_replace('{ORDER_TOTAL}', $data['orderTotal'], $template);
                $template = str_replace('{NEW_STATUS}', $data['newStatus'], $template);
                break;
        }
        
        return $template;
    }    
}