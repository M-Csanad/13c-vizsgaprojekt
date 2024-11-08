<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";
if ($searchTerm) {
    include_once "../auth/init.php";
    
    $matches = selectData("SELECT product.id, product.name, category.name as category, subcategory.name as subcategory
                           FROM product 
                           INNER JOIN product_page ON product.id = product_page.product_id 
                           INNER JOIN category ON product_page.category_id = category.id 
                           INNER JOIN subcategory ON product_page.subcategory_id = subcategory.id
                           WHERE product.name LIKE ?;", $searchTerm);
    if (!is_array($matches)) {
        echo json_encode($matches, JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!isset($matches[0])) $matches = [$matches];
    foreach ($matches as &$match) {
        $image = selectData("SELECT image.uri 
                            FROM `image` 
                            INNER JOIN product_image 
                                ON image.id = product_image.image_id 
                            WHERE product_image.product_id = ? 
                            AND image.orientation='horizontal'
                            AND image.media_type='image'
                            LIMIT 1;", $match["id"]);

        $match['thumbnail_image_horizontal_uri'] = $image['uri'];
    }

    echo json_encode($matches, JSON_UNESCAPED_UNICODE);
}

?>