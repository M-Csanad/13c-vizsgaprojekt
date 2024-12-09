<?php
function isMaintenanceMode(): bool {
    static $cache = null;

    if ($cache === null) {
        $cache = file_exists(__DIR__ . '/.maintenance');
    }

    return $cache;
}
?>
