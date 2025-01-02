<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode("Hibás metódus! Várt: GET, Aktuális: ".$_SERVER["REQUEST_METHOD"], JSON_UNESCAPED_UNICODE);
    header("bad request", true, 400);
    return;
}

if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
    header("bad request", true, 400);
    echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
    return;
}

include_once __DIR__."/../init.php";

$searchTerm = $_POST['search_term'];
$searchTerm = "%".$searchTerm."%";

$matches = selectData("SELECT product_page.id, product_page.page_title AS name, SUBSTRING(product_page.page_content, 1, 25) AS content_preview, product_page.page_content, 
                        category.name AS category_name, subcategory.name AS subcategory_name, image.uri, product_page.category_id, product_page.subcategory_id FROM product_page 
                        INNER JOIN product ON product_page.product_id=product.id
                        LEFT JOIN category ON product_page.category_id=category.id 
                        LEFT JOIN subcategory ON product_page.subcategory_id=subcategory.id 
                        LEFT JOIN product_image ON product.id = product_image.product_id 
                        LEFT JOIN image ON product_image.image_id = image.id 
                        AND image.media_type='image'
                        AND image.uri LIKE '%thumbnail%'
                        WHERE ( product.name LIKE ? 
                        OR product_page.page_title LIKE ? 
                        OR product_page.link_slug LIKE ? )
                        GROUP BY product_page.id
                        ORDER BY product_page.page_title;", 
                        array_fill(0, 3, $searchTerm), "sss");

echo json_encode($matches, JSON_UNESCAPED_UNICODE);

?>