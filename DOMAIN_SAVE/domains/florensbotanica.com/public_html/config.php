<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
define('BASE_PATH', realpath(__DIR__));


include BASE_PATH  . '/fb-functions/maintenance/maintenanceVerify.php';

if (isMaintenanceMode()) {
    http_response_code(503); // HTTP 503: Szolgáltatás nem elérhető
    include BASE_PATH  . '/fb-content/maintenance.php';
    exit;
}