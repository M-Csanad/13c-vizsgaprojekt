<?php
include_once 'init.php';

// Kategória ellenőrzés
function isValidCategory($name) {
    $result = selectData("SELECT 1 FROM category WHERE TRIM(category.name) LIKE CONVERT(? using utf8mb4) COLLATE utf8mb4_general_ci", reverse_format_str($name), "s");
    return typeOf($result, "SUCCESS");
}

// Alategória ellenőrzés
function isValidSubcategory($name, $parents) {
    $result = selectData("SELECT 1 FROM subcategory INNER JOIN category ON subcategory.category_id = category.id WHERE TRIM(subcategory.name) LIKE CONVERT(? using utf8mb4) AND TRIM(category.name) LIKE CONVERT(? using utf8mb4)", [reverse_format_str($name), reverse_format_str($parents[0])], "ss");
    return typeOf($result, "SUCCESS");
}

// Termék ellenőrzés
function isValidProduct($name, $parents) {
    $result = selectData("SELECT 1 FROM product_page WHERE product_page.link_slug=CONVERT(? using utf8mb4)", implode("/", $parents)."/$name","s");
    return typeOf($result, "SUCCESS");
}