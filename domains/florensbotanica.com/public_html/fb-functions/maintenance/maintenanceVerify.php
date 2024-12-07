<?php
session_start();

// Hibaüzenetek naplózása egyedi log fájlba
$logFile = __DIR__ . '/.maintenanceVerify.log';
function logError($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

// .env fájl betöltése
$envPath = realpath(__DIR__ . '/../../../.ext/.env');
$ipEnvPath = realpath(__DIR__ . '/../../../.ext/.ip.env');

if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
    $validUsername = $env['SITE_USERNAME'] ?? '';
    $validPassword = $env['SITE_PASSWORD'] ?? '';
    $dbServername = $env['DB_SERVERNAME'] ?? '';
    $dbName = $env['DB_BAN_NAME'] ?? '';
    $dbUsername = $env['DB_BAN_USERNAME'] ?? '';
    $dbPassword = $env['DB_BAN_PASSWORD'] ?? '';
} else {
    $errorMessage = 'Karbantartás alatt.';
    logError($errorMessage);
    return;
}

// IP-k betöltése külön fájlból
if ($ipEnvPath && file_exists($ipEnvPath)) {
    $ipEnv = parse_ini_file($ipEnvPath);
    $adminIPv4s = isset($ipEnv['ADMIN_IPV4S']) ? explode(',', $ipEnv['ADMIN_IPV4S']) : [];
    $adminIPv6s = isset($ipEnv['ADMIN_IPV6S']) ? explode(',', $ipEnv['ADMIN_IPV6S']) : [];
} else {
    $adminIPv4s = [];
    $adminIPv6s = [];
}

// Felhasználó IP-címének lekérése
$userIP = $_SERVER['REMOTE_ADDR'];

// IP cím típusának meghatározása
if (!filter_var($userIP, FILTER_VALIDATE_IP)) {
    $errorMessage = 'Érvénytelen IP cím.';
    logError($errorMessage);
    return;
}

// Admin IP-k esetén nincs tiltás
$isAdminIP = (filter_var($userIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && in_array($userIP, $adminIPv4s)) ||
             (filter_var($userIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && in_array($userIP, $adminIPv6s));

// Adatbázis kapcsolat létrehozása
$mysqli = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

if ($mysqli->connect_error) {
    $errorMessage = 'Adatbázis kapcsolat sikertelen: ' . $mysqli->connect_error;
    logError($errorMessage);
    return;
}

// Bannolt IP ellenőrzése
if (!$isAdminIP) {
    $stmt = $mysqli->prepare("SELECT ban_count, permanent, banned_until FROM banned_ips WHERE ip_address = ?");
    $stmt->bind_param('s', $userIP);
    $stmt->execute();
    $stmt->bind_result($banCount, $permanent, $bannedUntil);
    $stmt->fetch();
    $stmt->close();

    if (isset($banCount)) {
        if ($permanent) {
            $errorMessage = 'Az IP címed véglegesen tiltva van.';
            logError($errorMessage);
            return;
        }

        $banEndTime = new DateTime($bannedUntil);
        $currentTime = new DateTime();

        if ($banEndTime > $currentTime) {
            $errorMessage = 'Az IP címed tiltva van. Próbálkozz később!';
            logError($errorMessage);
            return;
        }
    }
}

// Hibaszámláló inicializálása
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

// Bejelentkezési validáció
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['authenticated'] = true;
        $_SESSION['attempts'] = 0;
        header('Location: succesfulLogin.php');
        exit;
    } else {
        $_SESSION['attempts']++;
        if ($_SESSION['attempts'] >= 3 && !$isAdminIP) {
            $banDuration = new DateInterval('PT12H');
            $banEndTime = (new DateTime())->add($banDuration)->format('Y-m-d H:i:s');

            $stmt = $mysqli->prepare("INSERT INTO banned_ips (ip_address, ban_count, permanent, banned_until) VALUES (?, ?, ?, ?)
                                      ON DUPLICATE KEY UPDATE ban_count = ban_count + 1, banned_until = VALUES(banned_until)");
            $stmt->bind_param('siis', $userIP, $_SESSION['attempts'], 0, $banEndTime);
            $stmt->execute();
            $stmt->close();

            $errorMessage = 'Túl sok sikertelen próbálkozás. Az IP címed ideiglenesen tiltva lett.';
            logError($errorMessage);
            return;
        }

        $errorMessage = "Helytelen felhasználónév vagy jelszó. Hátralévő próbálkozások: " . (3 - $_SESSION['attempts']);
        logError($errorMessage);
    }
}
?>
