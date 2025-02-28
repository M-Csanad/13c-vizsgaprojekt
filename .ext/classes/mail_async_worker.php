<?php
include_once 'mail.php';

// Parancssori argumentumból kapott ideiglenes fájl elérési útja
$tempFilePath = $argv[1];

// Ellenőrizzük, hogy létezik-e a fájl
if (!file_exists($tempFilePath)) {
    error_log("Temp fájl nem található: " . $tempFilePath);
    exit(1);
}

try {
    // JSON adatokat olvasunk az ideiglenes fájlból
    $jsonData = file_get_contents($tempFilePath);
    $data = json_decode($jsonData, true);
    
    if (!$data || !isset($data['recipient']) || !isset($data['mail'])) {
        throw new Exception("Érvénytelen adatformátum az ideiglenes fájlban");
    }
    
    // Kivonjuk a címzett és az email adatokat
    $recipient = $data['recipient'];
    $mail = $data['mail'];
    
    // Feldolgozzuk az emailt
    $mailer = new Mail();
    $result = $mailer->sendTo($recipient, $mail, false);
    
    // Siker vagy hiba naplózása
    if ($result->isError()) {
        log_Error("Hiba küldéskor: " . $result->toJSON(true), "mail_async_worker.txt");
    }
} catch (Exception $e) {
    log_Error("Hiba küldéskor: " . $result->toJSON(true), "mail_async_worker.txt");
} finally {
    // Mindig töröljük az ideiglenes fájlt, ha végeztünk
    if (file_exists($tempFilePath)) {
        unlink($tempFilePath);
    }
}