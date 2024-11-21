<?php
    

// Link Slug (pl. www.oldalam.hu << /aloldalam >>) létrehozása
function getLinkSlug($name, $categoryData) {

    // Formátum: főkategória/alkategória/terméknév
    $link_slug = format_str($categoryData["category"]) . "/" . format_str($categoryData["subcategory"]) . "/" . format_str($name);

    return array(
        "link_slug" => $link_slug
    );
}

// A termékadatok feltöltése a `product_page` táblába
function uploadProductPageData($data) {

    $fields = array("product_id", "link_slug", "category_id", "subcategory_id", "page_title", "page_content");
    $values = array(
        $data['product_id'],
        $data['link_slug'],
        $data['category_id'],
        $data['subcategory_id'],
        $data['page_title'],
        $data['page_content']
    );
    
    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `product_page`($fieldList) VALUES ($placeholderList);";
    
    return updateData($query, $values);
}

function createProductPage($productData, $productPageData, $productCategoryData) {
    $result = getLinkSlug($productData['name'], $productCategoryData);
    if (is_array($result)) {
        $productPageData["link_slug"] = $result["link_slug"];
    }
    
    $productPageData["product_id"] = $productData["id"];
    $result = uploadProductPageData($productPageData);
    if (typeOf($result, "ERROR")) {
        if ($result["message"] -> getCode() === 1062) { // Duplicate entry hiba
            return "Ilyen termék oldal már létezik.";
        }
        else {
            return "Sikertelen feltöltés a product_page táblába. ({$result["message"]})";
        }
    }

    return true;
}

function removeProductPage($id) {
    return true;
}