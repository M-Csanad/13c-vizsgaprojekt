<?php
if (file_exists(__DIR__ . '/.maintenance')) {
    http_response_code(503);
    header('Location: /fb-content/maintenance.php');
    exit;
}
?>
