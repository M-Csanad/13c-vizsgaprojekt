<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$base_url = "http://localhost";
$footerData = [
    'logo' => "$base_url/fb-content/assets/media/images/logos/herbalLogo_white.png",
    'description' => 'A természet gyógyító erejét hozzuk el a modern wellness világába, tiszta, ájurvéda által inspirált termékeket kínálva a kiegyensúlyozott egészségért és jólétért.',
    'sections' => [
        'Hasznos oldalak' => [
            ['text' => 'Főoldal', 'url' => "$base_url"],
            ['text' => 'Kategóriák', 'url' => "$base_url#categoryGallery"],
            ['text' => 'Adatvédelem', 'url' => "$base_url/privacy-policy"],
            ['text' => 'Rólunk', 'url' => "$base_url/about-us"],
            ['text' => 'Szerzői jog', 'url' => '#'],
        ],
        'Csapatunk' => [
            ['name' => 'Csanád', 'url' => "$base_url/about-us", 'icon' => 'construct-outline'],
            ['name' => 'Máté', 'url' => "$base_url/about-us", 'icon' => 'construct-outline'],
        ],
        'Sitemap' => [
            ['text' => 'Keresés & Sitemap', 'url' => '#'],
            ['text' => 'Sitemap oldalak', 'url' => '#'],
        ],
    ],
    'copyright' => 'Copyright © 2024-2025 Florens Botanica, Egyéni Vállalkozó. A "Florens Botanica" márkanév és a cég logója, valamint a weboldal összes dizájn- és szöveges tartalma egyedileg készült. Minden jog fenntartva.'
];
header('Content-Type: application/json');
echo json_encode($footerData);
exit;
?>

