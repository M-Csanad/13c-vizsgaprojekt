<?php

$id = $_GET['id'] ?? '';

if ($id) {
    include_once __DIR__."/../init.php";

    $id = intval($id);

    $query = "SELECT i.id, REGEXP_REPLACE(i.uri, '\\.[^.]*$', '') as uri, (CASE WHEN i.uri LIKE '%thumbnail%' THEN true ELSE false END) AS is_thumbnail FROM product_image pi LEFT JOIN image i ON i.id=pi.image_id WHERE pi.product_id = ?;";
                           
    $matches = selectData($query, $id, "i"); 
    echo $matches->toJSON();
}