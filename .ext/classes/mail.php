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
            
            try {
                // Egyedi ideiglenes fájl létrehozása
                $tempFile = tempnam(sys_get_temp_dir(), 'mail_');
                
                // Adatok előkészítése asszociatív tömbként
                $data = [
                    'recipient' => $recipients,
                    'mail' => $mail
                ];
                
                // JSON adatok írása az ideiglenes fájlba
                file_put_contents($tempFile, json_encode($data, JSON_UNESCAPED_UNICODE));
                
                // PATH ellenőrzés és javítás
                $phpDir = "C:\\xampp\\php";
                $currentPath = getenv('PATH');
            
                if (strpos($currentPath, $phpDir) === false) {
                    putenv('PATH=' . $currentPath . ';' . $phpDir);
                }
            
                // Parancs elkészítése Windowsra és Linuxra
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $command = "start /B php $dir/mail_async_worker.php \"$tempFile\" > NUL 2>&1";
                } else {
                    $command = "php $dir/mail_async_worker.php \"$tempFile\" > /dev/null 2>&1 &";
                }
            
                // Háttérfolyamat indítása
                pclose(popen($command, "r"));
                
                return new Result(Result::SUCCESS, 'Email küldés aszinkron módban.');
            } catch (Exception $e) {
            // Ideiglenes fájl törlése, ha valami hiba történt
            if (isset($tempFile) && file_exists($tempFile)) {
                unlink($tempFile);
            }
            return new Result(Result::ERROR, 'Hiba az aszinkron email küldés során: ' . $e->getMessage());
            }
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
    public static function getMailBody(string $type, string $name, array $data): string {
        $template = '';
        
        switch ($type) {
            case 'order':
                // Betöltjük a megfelelő sablont
                $templateFile = Mail::TEMPLATE_PATH . $type . '.html';
                
                // Ellenőrizzük, hogy létezik-e a sablon fájl
                if (!file_exists($templateFile)) {
                    log_Error("Email sablon nem található: " . $templateFile, "mail_error.txt");
                    throw new Result(Result::ERROR, "Email sablon nem található: ".$templateFile);
                }
                
                // Betöltjük a sablon tartalmát
                $template = file_get_contents($templateFile);
                
                // Alapvető változók cseréje
                $template = str_replace('{NAME}', $name, $template);
                
                // Rendelés specifikus változók cseréje
                $template = str_replace('{ORDER_NUMBER}', $data['orderNumber'], $template);
                $template = str_replace('{ORDER_DATE}', $data['orderDate'], $template);
                $template = str_replace('{ORDER_TOTAL}', $data['orderTotal'], $template);
                
                // Termékek listájának generálása
                $itemsHtml = '';
                foreach ($data['items'] as $item) {
                    $subtotal = $item['unit_price'] * $item['quantity'];
                    $itemsHtml .= "<td style='text-align: center;'>{$item['name']}</td><td style='text-align: center;'>{$item['quantity']} db</td><td style='text-align: center;'>{$subtotal} Ft</td></li>";
                }
                $template = str_replace('{ORDER_ITEMS}', $itemsHtml, $template);
                break;
                
            case 'reset_password':
                // Betöltjük a megfelelő sablont
                $templateFile = Mail::TEMPLATE_PATH . $type . '.html';
                
                // Ellenőrizzük, hogy létezik-e a sablon fájl
                if (!file_exists($templateFile)) {
                    log_Error("Email sablon nem található: " . $templateFile, "mail_error.txt");
                    throw new Result(Result::ERROR, "Email sablon nem található: ".$templateFile);
                }
                
                // Betöltjük a sablon tartalmát
                $template = file_get_contents($templateFile);
                
                // Alapvető változók cseréje
                $template = str_replace('{NAME}', $name, $template);
                
                // Jelszó visszaállítás specifikus változók cseréje
                $resetLink = "http://localhost/reset?token={$data['token']}";
                $template = str_replace('{RESET_LINK}', $resetLink, $template);
                break;

            case 'status_update':     
                $templateFile = Mail::TEMPLATE_PATH . $type . '.html';

                // Ellenőrizzük, hogy létezik-e a sablon fájl
                if (!file_exists($templateFile)) {
                    log_Error("Email sablon nem található: " . $templateFile, "mail_error.txt");
                    throw new Result(Result::ERROR, "Email sablon nem található: ".$templateFile);
                }
                
                // Betöltjük a status_update.html template-et
                $template = file_get_contents($templateFile);
                
                // Alapvető változók cseréje
                $template = str_replace('{NAME}', $name, $template);
                $template = str_replace('{ORDER_NUMBER}', $data['orderNumber'], $template);
                $template = str_replace('{ORDER_DATE}', $data['orderDate'], $template);
                $template = str_replace('{ORDER_TOTAL}', $data['orderTotal'], $template);
                $template = str_replace('{NEW_STATUS}', $data['newStatus'], $template);
                
                // Státuszok frissítése a megfelelő jelölésekkel
                $statusIds = ['processing', 'shipping', 'completed'];
                
                // Státuszok beállítása a jelenlegi állapot alapján
                for ($i = 1; $i <= min($data['statusIndex'], 3); $i++) {
                    // A státuszokhoz tartozó CSS felülírások
                    $statusId = $statusIds[$i-1];
                    
                    // Kör ikon színének változtatása
                    $template = str_replace(
                        "id=\"{$statusId}-circle\" style=\"width: 30px; height: 30px; background-color: #f1f1f1;", 
                        "id=\"{$statusId}-circle\" style=\"width: 30px; height: 30px; background-color: #9acd32;", 
                        $template
                    );
                    
                    // Szöveg színének változtatása
                    $template = str_replace(
                        "id=\"{$statusId}-text\" style=\"margin: 0 0 5px 0; color: #666;", 
                        "id=\"{$statusId}-text\" style=\"margin: 0 0 5px 0; color: #9acd32;", 
                        $template
                    );
                }
                break;

            default:
                // Betöltjük a megfelelő sablont
                $templateFile = Mail::TEMPLATE_PATH . $type . '.html';
                
                // Ellenőrizzük, hogy létezik-e a sablon fájl
                if (!file_exists($templateFile)) {
                    log_Error("Email sablon nem található: " . $templateFile, "mail_error.txt");
                    throw new Result(Result::ERROR, "Email sablon nem található: ".$templateFile);
                }
                
                // Betöltjük a sablon tartalmát
                $template = file_get_contents($templateFile);
                
                // Alapvető változók cseréje
                $template = str_replace('{NAME}', $name, $template);
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
    public static function getMailAltBody(string $type, string $name, array $data) {
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