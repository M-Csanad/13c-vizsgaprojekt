<?php

$env = parse_ini_file("./.env");
$dbServername = $env['DB_SERVERNAME'] ?? '';
$dbName = $env['DB_MAIN_NAME'] ?? '';
$dbUsername = $env['DB_MAIN_USERNAME'] ?? '';
$dbPassword = $env['DB_MAIN_PASSWORD'] ?? '';

define("DB_HOST", $dbServername);
define("DB_USER", $dbUsername);
define("DB_PASSWORD", $dbPassword);
define("DB_NAME", $dbName);

?>