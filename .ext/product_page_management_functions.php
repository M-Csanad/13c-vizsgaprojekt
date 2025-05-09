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
    
    return updateData($query, $values, "isiiss");
}

function createProductPage($productData, $productPageData, $productCategoryData) {
    $result = getLinkSlug($productData['name'], $productCategoryData);
    if (is_array($result)) {
        $productPageData["link_slug"] = $result["link_slug"];
    }
    
    $productPageData["product_id"] = $productData["id"];
    $result = uploadProductPageData($productPageData);
    if ($result->isError()) {
        if ($result->code === 1062) { // Duplicate entry hiba
            return new Result(Result::ERROR, "Ilyen termék oldal már létezik.");
        }
        else {
            return new Result(Result::ERROR, "Sikertelen feltöltés a product_page táblába: {$result->toJSON()}");
        }
    }

    return new Result(Result::SUCCESS, "Sikeres termék oldal létrehozás.");
}

function removeProductPage($id) {
    return updateData("DELETE FROM product_page WHERE product_page.id=?;", $id, "i");
}

function modifyProductPage($pageData, $categoryData) {
    $newSlug = getLinkSlug($pageData["page_title"], $categoryData);
    $data = array($newSlug["link_slug"], $pageData["category_id"], $pageData["subcategory_id"], $pageData["page_content"], $pageData["id"]);
    return updateData("UPDATE product_page SET link_slug=?, category_id=?, subcategory_id=?, page_content=? WHERE product_page.id=?", $data, "siisi");
}