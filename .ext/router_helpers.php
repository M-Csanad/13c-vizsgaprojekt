<?php
include_once 'init.php';

// Kategória ellenőrzés
function isValidCategory($name) {
    $result = selectData("SELECT 1 FROM category WHERE TRIM(category.name) LIKE ?", reverse_format_str($name), "s");
    return typeOf($result, "SUCCESS");
}

// Alategória ellenőrzés
function isValidSubcategory($name, $parents) {
    $result = selectData("SELECT 1 FROM subcategory INNER JOIN category ON subcategory.category_id = category.id WHERE TRIM(subcategory.name) LIKE ? AND TRIM(category.name) LIKE ?", [reverse_format_str($name), reverse_format_str($parents[0])], "ss");
    return typeOf($result, "SUCCESS");
}

// Termék ellenőrzés
function isValidProduct($name, $parents) {
    $result = selectData("SELECT 1 FROM product_page WHERE product_page.link_slug=?", implode("/", $parents)."/$name","s");
    return typeOf($result, "SUCCESS");
}