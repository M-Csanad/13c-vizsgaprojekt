<?php

$parsedUrl = parse_url($_SERVER['REQUEST_URI']);
$path = $parsedUrl['path'];

$routes = [
    '/api/cart/add' => $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/misc/cart_add.php',
    '/api/cart/remove' => $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/misc/cart_remove.php',
    '/api/cart/update' => $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/misc/cart_update.php',
    '/api/cart/upload' => $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/misc/cart_upload.php',
    '/api/review' => 'review.php',
    '/api/auth/dashboard-search' => 'dashboard_search.php'
];

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
