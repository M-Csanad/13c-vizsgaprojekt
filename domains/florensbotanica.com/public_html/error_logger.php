<?php
/**
 * Hibaüzenet naplózása egy megadott nevű naplófájlba.
 *
 * @param string $message A naplózandó hibaüzenet.
 * @param string $logFileName A naplófájl egyedi neve (pl. 'myfile_error.log').
 * @param string $logDirectory A naplófájl mappájának elérési útja. Alapértelmezett: 'log'.
 * @return void
 */
function logError($message, $logFileName, $logDirectory = './fb-log')
{
    $fallbackLog = "$logDirectory/fallback_error.log";

    $logFilePath = rtrim($logDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $logFileName;

    // Ellenőrizzük, hogy a mappa létezik-e, ha nem, létrehozzuk
    if (!file_exists($logDirectory)) {
        if (!mkdir($logDirectory, 0750, true)) {
            error_log("Nem sikerült létrehozni a naplófájl mappáját: $logDirectory. Hiba: $message", 3, $fallbackLog);
            return;
        }
    }

    // Ellenőrizzük, hogy a fájl létezik-e, ha nem, létrehozzuk
    if (!file_exists($logFilePath)) {
        if (!touch($logFilePath)) {
            error_log("Nem sikerült létrehozni a naplófájlt: $logFilePath. Hiba: $message", 3, $fallbackLog);
            return;
        }
        if (!chmod($logFilePath, 0640)) {
            error_log("Nem sikerült beállítani a naplófájl jogosultságait: $logFilePath. Hiba: $message", 3, $fallbackLog);
            return;
        }
    }

    // Időbélyeg hozzáadása az üzenethez - átláthatóság és debug miatt
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[$timestamp] $message" . PHP_EOL;

    // Üzenet naplózása a megadott fájlba
    if (!error_log($formattedMessage, 3, $logFilePath)) {
        error_log("Nem sikerült írni a naplófájlba: $logFilePath. Hiba: $message", 3, $fallbackLog);
    }
}


?>

