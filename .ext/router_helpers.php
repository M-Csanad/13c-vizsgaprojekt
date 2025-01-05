<?php
include_once 'init.php';

// Kategória ellenőrzés
function isValidCategory($name) {
    $result = selectData("SELECT category.id FROM category WHERE category.slug=?", $name, "s");
    return $result;
}

// Alategória ellenőrzés
function isValidSubcategory($name, $parents) {
    $result = selectData("SELECT subcategory.id FROM subcategory INNER JOIN category ON subcategory.category_id = category.id WHERE category.slug=? AND subcategory.slug=?", [$parents[0], $name], "ss");
    return $result;
}

// Termék ellenőrzés
function isValidProduct($name, $parents) {
    $result = selectData("SELECT product.id FROM product_page WHERE product_page.link_slug=?", implode("/", $parents)."/$name","s");
    return $result;
}