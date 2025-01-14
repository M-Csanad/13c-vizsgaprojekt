<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/init.php';
// Felhasználó adatainak lekérdezése
$result = getUserData();
$isLoggedIn = false;
$user = null;

if ($result->isSuccess()) {
    $user = $result->message[0];
    $isLoggedIn = true;
}
