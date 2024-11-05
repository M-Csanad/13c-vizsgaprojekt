<?php

function createProduct($productData, $productPageData) {
    include_once "init.php";

    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $paths = createProductDirectory($productData);
    $result = uploadProductData($productData);

    if (is_numeric($result)) {
        $productData["id"] = $result;
        $productPageData["product_id"] = $result;
    }
    else {
        return "Sikertelen feltöltés a product táblába. ($result)";
    }

    $result = getLinkSlug($productData['id']);
    if (is_array($result)) {
        $productPageData["category_id"] = $result["category_id"];
        $productPageData["subcategory_id"] = $result["subcategory_id"];
        $productPageData["link_slug"] = $result["link_slug"];
    }


    $result = uploadProductPageData($productPageData);
    if (!is_numeric($result)) {
        return "Sikertelen feltöltés a product_page táblába. ($result)";
    }
}

function getLinkSlug($id, $name) {
    $result = selectData("SELECT category.name as category_name, 
                                 subcategory.name as subcategory_name,
                                 product_page.category_id,
                                 product_page.subcategory_id,
                          FROM product_page
                          INNER JOIN category ON product_page.category_id = category.id 
                          INNER JOIN subcategory ON product_page.subcategory_id = subcategory.id 
                          WHERE product_page.id = ?;", $id);

    if (is_array($result)) {
        $link_slug = format_str($result["category_name"]) . "/" . format_str($result["subcategory_id"]) . "/" . format_str($name);
        return array(
            "category_id" => $result["category_id"],
            "subcategory_id" => $result["subcategory_id"],
            "link_slug" => $link_slug
        );
    }
}

function createProductDirectory($productData) {

    $baseDirectory = './images/products/';

    $productName = str_replace(" ", "-", mb_strtolower($productData['name']));
    $productDirURI = $baseDirectory.$productName."/";
    
    $successfulDirectoryCreate = createDirectory([$productDirURI,$productDirURI.'thumbnail/', $productDirURI.'gallery/']);

    if (!$successfulDirectoryCreate) {
        echo "<div class='error'>Ilyen nevű termék már létezik.</div>";
        return false;
    }

    $thumbnailTmp = $_FILES['thumbnail_image']['tmp_name'];
    $thumbnail = $_FILES['thumbnail_image']['name'];
    $extension = pathinfo($thumbnail, PATHINFO_EXTENSION);

    $successfulUpload = move_uploaded_file($thumbnailTmp, $productDirURI."thumbnail/thumbnail." . $extension);
    if (!$successfulUpload) {
        return false;
    }

    $fileCount = count($_FILES['product_images']['name']);
    for ($i=0; $i < $fileCount; $i++) {
        $productImageTmp = $_FILES['product_images']['tmp_name'][$i];
        $productImage = $_FILES['product_images']['name'][$i];
        $extension = pathinfo($productImage, PATHINFO_EXTENSION);

        $successfulUpload = move_uploaded_file($productImageTmp, $productDirURI."gallery/image" . $i . "." . $extension);
        if (!$successfulUpload) {
            return false;
        }
    }

    return true;
}

function uploadProductData($data) {
    $fields = array("name", "unit_price", "stock", "description");

    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `product`($fieldList) VALUES ($placeholderList);";
    
    return updateData($query, $values);
}

function uploadProductPageData($data) {
    var_dump($data);
    // $fields = array("product_id", "link_slug", "category_id", "subcategory_id", "page_title", "page_content");
    
    // $fieldList = implode(", ", $fields);
    // $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    // $query = "INSERT INTO `product_page`($fieldList) VALUES ($placeholderList);";
    
    // return updateData($query, $values);
}

function removeProduct($productData) {
    include_once 'init.php';

    // A kategória törlése az adatbázisból
    $successfulDelete = removeProductFromDB($productData);
    if ($successfulDelete === false) return "Ez a termék nem létezik!";

    else if ($successfulDelete !== true) return $successfulDelete;

    // A kategória mappájának törlése
    $successfulDirectoryDelete = removeProductDirectory($productData);
    if (!$successfulDirectoryDelete) return "A mappa törlése sikertelen! (A mappát manuálisan kell törölni).";

    return $successfulDirectoryDelete;
}

function removeProductFromDB($productData) {
    $db = createConnection();
    
    $successfulDelete = updateData("DELETE FROM product WHERE product.id = ?;", $productData['id']);

    return $successfulDelete;
}

function removeProductDirectory($productData) {
    $baseDir = "./images/products/";
    $productName = format_str($productData['name']);

    $productDirURI = $baseDir.$productName."/";

    $successfulDelete = deleteFolder($productDirURI);

    return $successfulDelete;
}

?>