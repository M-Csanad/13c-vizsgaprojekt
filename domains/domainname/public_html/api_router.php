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
    // Kosár
    '/api/cart/add'                 => $API.'/cart_add.php',
    '/api/cart/get'                 => $API.'/cart_get.php',
    '/api/cart/check'               => $API.'/cart_check.php',
    '/api/cart/remove'              => $API.'/cart_remove.php',
    '/api/cart/update'              => $API.'/cart_update.php',
    '/api/cart/merge'               => $API.'/cart_merge.php',

    // Rendelés
    '/api/order/place'              => $API.'/order_place.php',
    '/api/order/get'                => $API.'/order_get.php',

    // Értékelés
    '/api/review'                   => $API.'/review.php',
    '/api/product/reviews'          => $API.'/get_product_reviews.php',

    // Automatikus kitöltés
    '/api/autofill/add'             => $API.'/autofill_add.php',
    '/api/autofill/get'             => $API.'/autofill_get.php',
    '/api/autofill/remove'          => $API.'/autofill_remove.php',
    '/api/autofill/update'          => $API.'/autofill_update.php',

    // Hitelesítendő végpontok
    '/api/auth/dashboard-search'    => $API.'/dashboard_search.php',
    '/api/auth/login'               => $API.'/login.php',
    '/api/auth/register'            => $API.'/register.php',
    '/api/auth/resetpassword'       => $API.'/reset_password.php',

    // Beállítások
    '/api/settings/updatedetails'   => $API.'/settings_update_details.php',
    '/api/settings/newpassword'     => $API.'/settings_new_password.php',

    // Keresés
    '/api/search'                   => $API.'/search.php',
    '/api/subcategory/products'     => $API.'/subcategory_products.php',
    '/api/images'                   => $API.'/image_search.php'
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
