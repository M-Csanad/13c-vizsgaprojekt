<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";
if ($searchTerm) {
    include_once "../auth/init.php";

    $matches = selectData("SELECT 'category' AS 'type',
                                   category.id, category.name AS `name`, category.description, category.thumbnail_image_uri, NULL AS parent_category
                           FROM category 
                           WHERE category.name LIKE ?
                                OR category.description LIKE ? 
                           UNION 
                           SELECT 'subcategory' AS 'type', 
                                   subcategory.id, subcategory.name AS `name`, subcategory.description, subcategory.thumbnail_image_uri, category.name AS parent_category 
                           FROM subcategory 
                           INNER JOIN category ON subcategory.category_id = category.id
                           WHERE subcategory.name LIKE ? 
                                OR subcategory.description LIKE ?
                           ORDER BY 'type', `name`;", array_fill(0, 4, $searchTerm));
    echo json_encode($matches);
}

?>