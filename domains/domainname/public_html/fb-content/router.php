<?php

include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
include_once $_SERVER["DOCUMENT_ROOT"] . '/../../../.ext/router_helpers.php';

// URI szegmensek kiszedése
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $uri);

// EZT MAJD SZERVEREN KI KELL VENNI (ott nem lesznek felesleges url szegmensek)
// $segments = array_slice($segments, 4);
// !!

// Validációs 'térkép' létrehozása
$validationMap = [
    'category' => ['isValidCategory', '/fb-content/category.php'],
    'subcategory' => ['isValidSubcategory', '/fb-content/subcategory.php'],
    'product' => ['isValidProduct', '/fb-content/product.php'],
];

// Az URI-ban ha több szegmens található a végződés után,
// pl. www.teszt.com/elso/masodik/szegmens, akkor a szülőket beletesszük ebbe.
$parents = [];

// A validált elemek (pl. kategória vagy alkategória) azonosítóját eltároljuk,
// hogy az oldalakon fel tudjuk használni a lekérdezéselhez.
$ids = [];

// Minden szegmensen végigmegyünk
foreach ($segments as $level => $segment) {
    $key = array_keys($validationMap)[$level] ?? null;
    if (is_null($key)) {
        show404($_SERVER["DOCUMENT_ROOT"]);
    }

    $validationFunction = $validationMap[$key][0];
    $page = $validationMap[$key][1];

    $result = call_user_func_array($validationFunction, [$segment, $parents]);
    if (!$result->isSuccess()) {
        show404($_SERVER["DOCUMENT_ROOT"]);
    }

    $parents[] = $segment;
    $ids[] = $result->message[0]["id"];
}

// A betöltendő oldal elérési útvonalának kinyerése
$keys = array_keys($validationMap);

if (count($segments) <= count($keys)) {
    $page = $validationMap[$keys[count($segments) - 1]][1];
    include $_SERVER["DOCUMENT_ROOT"] . $page; // Az oldal betöltése
} else {
    show404($_SERVER["DOCUMENT_ROOT"]);
}

// 404-es oldalra való átirányítás
function show404($url)
{
    http_response_code(404);
    include $url . "/fb-functions/error/error-404.html";
    exit;
}
