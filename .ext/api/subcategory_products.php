<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['url'])) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Hiányzó URL paraméter');
    echo $result->toJSON();
    exit();
}

// URL szegmentálása
$segments = explode('/', ltrim($data['url'], '/'));

// Ha nem 2 elemű az URL, akkor biztos, hogy nem alkategória
if (count($segments) !== 2) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Hibás URL formátum');
    echo $result->toJSON();
    exit();
}

// URL adatok kinyerése
$parents = array_slice($segments, 0, 1);
$subcategory = array_slice($segments, 1, 1)[0];

// Alkategória azonosító lekérése
$result = isValidSubcategory($subcategory, $parents);
if ($result->isError() || $result->isEmpty()) {
    http_response_code(400);
    $result = new Result(Result::ERROR, 'Érvénytelen alkategória');
    echo $result->toJSON();
    exit();
}

$subcategoryId = $result->message[0]['id'];

// Termékek lekérése
$query = "SELECT 
    product.*, 
    product_page.link_slug,
    MAX(CASE 
        WHEN image.uri LIKE '%thumbnail%' THEN REGEXP_REPLACE(image.uri, '\\.[^.]*$', '')
    END) AS thumbnail_image,
    MAX(CASE 
        WHEN image.uri LIKE '%vertical%' THEN REGEXP_REPLACE(image.uri, '\\.[^.]*$', '')
        WHEN image.uri NOT LIKE '%vertical%' AND image.uri NOT LIKE '%thumbnail%' 
        THEN REGEXP_REPLACE(image.uri, '\\.[^.]*$', '')
    END) AS secondary_image,
    COALESCE(AVG(review.rating), 0) as avg_rating,
    COUNT(DISTINCT review.id) as review_count
FROM product_page 
INNER JOIN product ON product_page.product_id = product.id
LEFT JOIN product_image ON product.id = product_image.product_id
LEFT JOIN image ON product_image.image_id = image.id
LEFT JOIN review ON product.id = review.product_id
WHERE product_page.subcategory_id = ?
GROUP BY product.id 
ORDER BY product.name ASC";

$result = selectData($query, [$subcategoryId], "i");
if ($result->isError()) {
    http_response_code(500);
    echo $result->toJSON();
    exit();
}


if ($result->isSuccess() && !$result->isEmpty()) {
    
    foreach ($result->message as &$product) {
        $product['thumbnail_image'] = preg_replace('/\.[a-zA-Z0-9]+$/', '', $product['thumbnail_image']);
        $product['secondary_image'] = preg_replace('/\.[a-zA-Z0-9]+$/', '', $product['secondary_image']);
    }
}

echo $result->toJSON();
