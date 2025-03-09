<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";
if ($searchTerm) {
    include_once __DIR__."/../init.php";
    
    $query = "SELECT 
        product.id, 
        product.name, 
        product.description,
        product.unit_price, 
        product.stock, 
        category.name AS category, 
        GROUP_CONCAT(DISTINCT product_tag.tag_id) AS tag_ids, 
        subcategory.name AS subcategory, 
        REGEXP_REPLACE(image.uri, '\\.[^.]+$', '') AS thumbnail_image_horizontal_uri,
        GROUP_CONCAT(DISTINCT CASE WHEN health_effect.benefit = 1 THEN health_effect.id END) AS benefit_ids,
        GROUP_CONCAT(DISTINCT CASE WHEN health_effect.benefit = 0 THEN health_effect.id END) AS side_effect_ids
    FROM product
    LEFT JOIN product_page ON product.id = product_page.product_id
    LEFT JOIN category ON product_page.category_id = category.id
    LEFT JOIN subcategory ON product_page.subcategory_id = subcategory.id
    LEFT JOIN product_image ON product.id = product_image.product_id
    LEFT JOIN image ON product_image.image_id = image.id
    LEFT JOIN product_tag ON product.id = product_tag.product_id
    LEFT JOIN product_health_effect ON product.id = product_health_effect.product_id 
    LEFT JOIN health_effect ON product_health_effect.health_effect_id = health_effect.id
    WHERE 
        (product.name LIKE ? 
        OR category.name LIKE ? 
        OR subcategory.name LIKE ?)
        AND image.media_type = 'image'
        AND image.uri LIKE '%thumbnail%'
    GROUP BY product.id
    ORDER BY product.name;
    ";

    $matches = selectData($query, array_fill(0, 3, $searchTerm), "sss");
                           
    echo $matches->toJSON();
}