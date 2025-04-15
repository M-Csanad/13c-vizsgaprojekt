<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
define('BASE_PATH', realpath(__DIR__));

if (!defined("ROOT_URL")) {
    define('ROOT_URL', 'http://localhost');
}

// Kimeneti pufferelés indítása a végrehajtás elején
ob_start();

// Funkció a JS védelmi fájl beillesztésére
function includeJsProtection($buffer) {
    if (preg_match('/<html/i', $buffer)) {
        // Szkript beillesztése a záró </head> tag előtt, ha létezik
        $pattern = '/<\/head>/i';
        $replacement = '<script disable-devtool-auto src="/JS_64697361626C652D646576746F6F6C.js"></script></head>';

        if (preg_match($pattern, $buffer)) {
            $buffer = preg_replace($pattern, $replacement, $buffer, 1);
        } else {
            // Fallback
            $pattern = '/<body[^>]*>/i';
            $replacement = '$0<script disable-devtool-auto src="/JS_64697361626C652D646576746F6F6C.js"></script>';

            if (preg_match($pattern, $buffer)) {
                $buffer = preg_replace($pattern, $replacement, $buffer, 1);
            } else {
                // Utolsó megoldás: a pufferbe való beillesztés
                $buffer = '<script disable-devtool-auto src="/JS_64697361626C652D646576746F6F6C.js"></script>' . $buffer;
            }
        }
    }

    return $buffer;
}

// Regisztrálja a függvényt, amely a parancsfájl leállításakor végrehajtódik, és feldolgozza a puffert.
register_shutdown_function(function() {
    // Puffer tartalmának kiolvasása és törlése
    $buffer = ob_get_clean();
    // Szkript injektálást
    if ($buffer !== false) {
        echo includeJsProtection($buffer);
    }
});

include BASE_PATH  . '/fb-functions/maintenance/maintenanceVerify.php';

if (isMaintenanceMode()) {
    http_response_code(503); // HTTP 503: Szolgáltatás nem elérhető
    include BASE_PATH  . '/fb-content/maintenance.php';
    exit;
}
