<?php

include_once $_SERVER["DOCUMENT_ROOT"].'/13c-vizsgaprojekt/.ext/constants.php';
include_once ROOT_PATH . '/config.php';
include_once ROOT_PATH . '/fb-content/router_helpers.php';

// URI szegmensek kiszedése
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $uri);

// EZT MAJD SZERVEREN KI KELL VENNI (ott nem lesznek felesleges url szegmensek)
$segments = array_slice($segments, 4);
// !!

// Validációs 'térkép' létrehozása
$validationMap = [
    'category' => ['isValidCategory', '/fb-content/pages/category.php'],
    'subcategory' => ['isValidSubcategory', '/fb-content/pages/subcategory.php'],
    'product' => ['isValidProduct', '/fb-content/pages/product.php'],
];

// Az URI-ban ha több szegmens található a végződés után, 
// pl. www.teszt.com/elso/masodik/szegmens, akkor a szülőket beletesszük ebbe.
$parents = [];

// Minden szegmensen végigmegyünk
foreach ($segments as $level => $segment) {
    $key = array_keys($validationMap)[$level] ?? null;

    if (is_null($key)) {
        show404(ROOT_PATH);
    }

    $validationFunction = $validationMap[$key][0];
    $page = $validationMap[$key][1];
    
    if (!call_user_func_array($validationFunction, [$segment, $parents])) {
        show404(ROOT_PATH);
    }
    
    $parents[] = $segment;
}

// A betöltendő oldal elérési útvonalának kinyerése
$keys = array_keys($validationMap);
if (count($segments) <= count($keys)) {
    $page = $validationMap[$keys[count($segments) - 1]][1];
    include ROOT_PATH.$page; // Az oldal betöltése
} else {
    show404(ROOT_PATH);
}

// 404-es oldalra való átirányítás
function show404() {
    http_response_code(404);
    include ROOT_PATH . "/fb-content/pages/error-404.html";
    exit;
}