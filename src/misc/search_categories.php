<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";
if ($searchTerm) {
    include "../auth/init.php";

    $matches = selectData("SELECT 'category' AS category_type,
                                   category.id, category.name, category.description, category.thumbnail_image_uri, NULL AS parent_category
                           FROM category 
                           WHERE category.name LIKE ?
                                OR category.description LIKE ? 
                           UNION 
                           SELECT 'subcategory' AS category_type, 
                                   subcategory.id, subcategory.name, subcategory.description, subcategory.thumbnail_image_uri, category.name AS parent_category 
                           FROM subcategory 
                           INNER JOIN category ON subcategory.category_id = category.id
                           WHERE subcategory.name LIKE ? 
                                OR subcategory.description LIKE ?;", array_fill(0, 4, $searchTerm));
    echo json_encode($matches);
}

?>