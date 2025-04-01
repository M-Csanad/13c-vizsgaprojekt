<?php

$id = $_GET['id'] ?? '';
$type = $_GET['type'] ?? '';

if ($id && $type) {
    include_once __DIR__."/../init.php";

    $id = intval($id);
    $type = strtolower($type);

    $query = "SELECT 
        REGEXP_REPLACE(thumbnail_image_horizontal_uri, '\\\\.[^.]+$', '') AS horizontal_uri, 
        REGEXP_REPLACE(thumbnail_image_vertical_uri, '\\\\.[^.]+$', '') AS vertical_uri 
    FROM $type WHERE id = ?;";
                           
    $matches = selectData($query, $id, "i"); 
    echo $matches->toJSON();
}