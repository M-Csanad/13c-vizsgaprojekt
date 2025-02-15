<?php
include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/user_config.php';


// Ha nincs keresési eredmény, akkor jelezzük, vagy átirányítjuk a felhasználót
var_dump(session_id(), $_SESSION);
/* var_dump($_SESSION); */
if (!isset($_SESSION['search_query']) || !isset($_SESSION['search_results'])) {
    echo "Nincs keresési eredmény. Kérlek végezz keresést!";
    exit;
}

$searchQuery = $_SESSION['search_query'];
$searchResults = $_SESSION['search_results'];
// Töröljük a session változókat, hogy később ne maradjanak fent
unset($_SESSION['search_query'], $_SESSION['search_results'], $_SESSION['search_error'], $_SESSION['search_info']);