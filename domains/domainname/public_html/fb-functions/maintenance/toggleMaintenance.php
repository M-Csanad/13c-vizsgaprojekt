<?php
$allowedIp = '127.0.0.1'; // Csak a szerver IP-je
if ($_SERVER['REMOTE_ADDR'] !== $allowedIp) {
    http_response_code(403);
    die('Hozzáférés megtagadva.');
}

$maintenanceFile = __DIR__ . '/.maintenance';

if (file_exists($maintenanceFile)) {
    unlink($maintenanceFile);
    echo 'Karbantartási mód kikapcsolva.';
} else {
    touch($maintenanceFile);
    echo 'Karbantartási mód bekapcsolva.';
}

function isMaintenanceMode(): bool {
    return file_exists(__DIR__ . '/.maintenance');
}
?>
