<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";
if ($searchTerm) {
    include_once "../auth/init.php";
    
    $query = "SELECT product.id, product.name, product.description,
                product.unit_price, product.stock, category.name AS category, GROUP_CONCAT(DISTINCT product_tag.tag_id) as tag_ids, 
                subcategory.name AS subcategory, image.uri AS thumbnail_image_horizontal_uri 
            FROM product 
            LEFT JOIN product_page ON product.id = product_page.product_id 
            LEFT JOIN category ON product_page.category_id = category.id 
            LEFT JOIN subcategory ON product_page.subcategory_id = subcategory.id 
            LEFT JOIN product_image ON product.id = product_image.product_id 
            LEFT JOIN product_tag ON product.id = product_tag.product_id 
            LEFT JOIN image ON product_image.image_id = image.id 
            WHERE (product.name LIKE ? 
            OR category.name LIKE ? OR subcategory.name LIKE ?)
            AND image.media_type = 'image'
            GROUP BY product.id
            ORDER BY product.name;";

    $matches = selectData($query, array_fill(0, 3, $searchTerm));
                           
    echo json_encode($matches, JSON_UNESCAPED_UNICODE);
}