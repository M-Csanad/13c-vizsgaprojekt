<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";

if ($searchTerm) {
    include_once __DIR__."/../init.php";
    $query = "SELECT 'category' AS `type`, category.id, category.name AS `name`, 
            category.subname, category.description, 
            REGEXP_REPLACE(category.thumbnail_image_horizontal_uri, '\\.[^.]+$', '') AS thumbnail_image_horizontal_uri, 
            NULL AS parent_category
            FROM category 
            WHERE category.name LIKE ?
            UNION 
            SELECT 'subcategory' AS 'type', subcategory.id, subcategory.name AS `name`, 
            subcategory.subname, subcategory.description, 
            REGEXP_REPLACE(subcategory.thumbnail_image_horizontal_uri, '\\.[^.]+$', '') AS thumbnail_image_horizontal_uri, 
            category.name AS parent_category 
            FROM subcategory 
            INNER JOIN category ON subcategory.category_id = category.id
            WHERE subcategory.name LIKE ? 
            ORDER BY type, name;";
                           
    $matches = selectData($query, array_fill(0, 2, $searchTerm), "ss");
                           
    echo $matches->toJSON();
}