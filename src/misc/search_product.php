<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";
if ($searchTerm) {
    include_once "../auth/init.php";
    
    $matches = selectData("SELECT product.id, product.name, category.name AS category, 
                                  subcategory.name AS subcategory, image.uri AS thumbnail_image_horizontal_uri 
                           FROM product 
                           INNER JOIN product_page ON product.id = product_page.product_id 
                           INNER JOIN category ON product_page.category_id = category.id 
                           INNER JOIN subcategory ON product_page.subcategory_id = subcategory.id 
                           LEFT JOIN product_image ON product.id = product_image.product_id 
                           LEFT JOIN image ON product_image.image_id = image.id 
                           WHERE product.name LIKE ? 
                           AND image.orientation = 'horizontal' 
                           AND image.media_type = 'image'
                           GROUP BY product.id;", 
                           [$searchTerm]);
                           
    echo json_encode($matches, JSON_UNESCAPED_UNICODE);
}

?>