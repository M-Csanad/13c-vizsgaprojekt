<?php
// Session indítása és majd a session lezárása a kijelentkezés érdekében
session_start();
session_destroy(); // Az aktuális session megsemmisítése (felhasználói adatok törlése)

// Ellenőrizze, hogy létezik-e a 'rememberMe' süti
if (isset($_COOKIE['rememberMe'])) {
    // A süti kezeléséhez szükséges funkciók betöltése
    include "./auth/cookie_session_functions.php";
    removeCookie($_COOKIE['rememberMe']); // A 'rememberMe' süti eltávolítása, ha létezik
}

// Új session indítása, hogy egy sikeres kijelentkezési üzenetet tároljon
session_start();
$_SESSION['successful_logout'] = true; // Beállítja, hogy a kijelentkezés sikeres volt

// Átirányítás a főoldalra a kijelentkezés után
header('Location: ./');
exit; // A script futásának leállítása, hogy biztosan ne fusson tovább
?>

