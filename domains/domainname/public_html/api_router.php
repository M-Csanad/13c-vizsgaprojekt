<?php

// Csak AJAX kéréseket fogad, egyéb esetben 404.
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(404);
    include $_SERVER["DOCUMENT_ROOT"]."/fb-functions/error/error-404.html";
    exit();
}

// A kérés URL-jéből kiszedjük az útvonalat
$parsedUrl = parse_url($_SERVER['REQUEST_URI']);
$path = $parsedUrl['path'];

// Az API kéréseket kiszolgáló mappa elérési útvonala
$API = $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/api';

// Végpontok és a kérést kezelő PHP fájl elérési útvonala
$routes = [
    '/api/cart/add' => $API.'/cart_add.php',
    '/api/cart/get' => $API.'/cart_get.php',
    '/api/cart/remove' => $API.'/cart_remove.php',
    '/api/cart/update' => $API.'/cart_update.php',
    '/api/cart/merge' => $API.'/cart_merge.php',
    '/api/order/place' => $API.'/order_place.php',
    '/api/order/get' => $API.'/order_get.php',
    '/api/review' => $API.'/review.php',
    '/api/autofill/add' => $API.'/autofill_add.php',
    '/api/autofill/get' => $API.'/autofill_get.php',
    '/api/auth/dashboard-search' => $API.'/dashboard_search.php',
    '/api/auth/login' => $API.'/login.php',
    '/api/auth/register' => $API.'/register.php',
];

// Ha az aktuális URL-t tartalmazza a $routes változó, akkor az egy létező végpont.
if (array_key_exists($path, $routes)) {
    $file = $routes[$path];
    if (file_exists($file)) {
        include $file;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Szerver oldali hiba, a hivatkozott fájl nem létezik.']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Végpont nem található.'], JSON_UNESCAPED_UNICODE);
}
