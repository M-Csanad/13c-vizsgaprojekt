<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once "../../../../.ext/init.php";
// Session indítása és majd a session lezárása a kijelentkezés érdekében
session_start();
session_destroy(); // Az aktuális session megsemmisítése (felhasználói adatok törlése)

// Ellenőrizze, hogy létezik-e a 'rememberMe' süti
if (isset($_COOKIE['rememberMe'])) {
    removeCookie($_COOKIE['rememberMe']); // A 'rememberMe' süti eltávolítása, ha létezik
}

// Új session indítása, hogy egy sikeres kijelentkezési üzenetet tároljon
session_start();
$_SESSION['successful_logout'] = true; // Beállítja, hogy a kijelentkezés sikeres volt

// Átirányítás a főoldalra a kijelentkezés után
header('Location: ./fb-auth/fb-admin/index.php');
exit(); // A script futásának leállítása, hogy biztosan ne fusson tovább
?>

