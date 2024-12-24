<?php

include_once "init.php";

function makeReview($reviewData) {
    // Forrás PHP fájl URL-jének kiszedése (termék azonosításához)
    $uri = trim(parse_url($reviewData['HTTP_REFERER'], PHP_URL_PATH), '/');
    $segments = explode('/', $uri); // 0: category, 1: subcategory, 2: product
    $slug = implode('/', $segments);
    
    // Termék és termékoldal adatainak lekérése
    $result = selectData("SELECT product.*, product_page.id as page_id, product_page.created_at, product_page.last_modified, product_page.page_title, product_page.page_content, category.name AS category_name, subcategory.name AS subcategory_name FROM product_page 
        INNER JOIN product ON product_page.product_id=product.id 
        INNER JOIN category ON product_page.category_id=category.id 
        INNER JOIN subcategory ON product_page.subcategory_id=subcategory.id 
        WHERE product_page.link_slug=?", $slug, "s");

    if (!typeOf($result, "SUCCESS")) {
        return ["message" => "Ismeretlen termék.", "type" => "ERROR"];
        exit;
    }
    $product = $result["message"][0];


    // Ellenőrizzük, hogy bejelentkezett felhasználó értékelt-e.
    session_start();
    if (!isset($_SESSION["user_id"])) return ["message" => "Értékelni csak bejelentkezett felhasználó tud!", "type" => "ERROR"];

    return updateData("INSERT INTO review (user_id, product_id, rating, description, title) VALUES (?, ?, ?, ?, ?);", [$_SESSION["user_id"], $product["id"], $reviewData["rating"], $reviewData["review-body"], $reviewData["review-title"]], "iidss");;
}