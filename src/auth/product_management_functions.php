<?php
/* --------------------------- Termék létrehozása --------------------------- */

// Link Slug (pl. www.oldalam.hu << /aloldalam >>) létrehozása
function getLinkSlug($name, $categoryData) {

    // Formátum: főkategória/alkategória/terméknév
    $link_slug = format_str($categoryData["category"]) . "/" . format_str($categoryData["subcategory"]) . "/" . format_str($name);

    return array(
        "link_slug" => $link_slug
    );
}

// Termékképek számára mappa létrehozása
function createProductDirectory($productData) {

    // A képek végső elérési útvonalait gyűjtjük
    $paths = array();
    $baseDirectory = './images/products/';

    $productName = format_str($productData['name']);
    $productDirURI = $baseDirectory.$productName."/";
    
    if (!createDirectory([$productDirURI,$productDirURI.'thumbnail/', $productDirURI.'gallery/'])) {
        return "Ilyen nevű termék már létezik.";
    }

    // A borítókép feltöltése
    $thumbnailTmp = $_FILES['thumbnail_image']['tmp_name'];
    $thumbnail = $_FILES['thumbnail_image']['name'];
    $extension = pathinfo($thumbnail, PATHINFO_EXTENSION);
    $path = $productDirURI."thumbnail/thumbnail." . $extension;

    if (!move_uploaded_file($thumbnailTmp, $path)) {
        return false;
    }

    array_push($paths, $path);

    // A termékvideó feltöltése
    $videoTmp = $_FILES['product_video']['tmp_name'];
    $video = $_FILES['product_video']['name'];
    $extension = pathinfo($video, PATHINFO_EXTENSION);
    $path = $productDirURI."thumbnail/thumbnail." . $extension;

    if (!move_uploaded_file($videoTmp, $path)) {
        return false;
    }

    array_push($paths, $path);

    // A termékképek feltöltése
    $fileCount = count($_FILES['product_images']['name']);
    for ($i = 0; $i < $fileCount; $i++) {

        $productImageTmp = $_FILES['product_images']['tmp_name'][$i];
        $productImage = $_FILES['product_images']['name'][$i];
        $extension = pathinfo($productImage, PATHINFO_EXTENSION);
        $path = $productDirURI."gallery/image" . $i . "." . $extension;

        if (!move_uploaded_file($productImageTmp, $productDirURI."gallery/image" . $i . "." . $extension)) {
            return false;
        }

        array_push($paths, $path);
    }

    return $paths;
}

// A termékadatok feltöltése a `product` táblába
function uploadProductData($data) {

    $fields = array("name", "unit_price", "stock", "description");
    $values = array(
        $data['name'],
        $data['unit_price'],
        $data['stock'],
        $data['description'],
    );

    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `product`($fieldList) VALUES ($placeholderList);";
    
    return updateData($query, $values);
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

// A termék képeinek feltöltése az `image` táblába
function uploadProductImages($paths) {

    // A feltöltött sorok azonosítóit elmentjük
    $insertIds = array();

    foreach ($paths as $path) {
        $mediaType = getMediaType($path);
        if (str_contains($mediaType, "video")) {
            $orientation = "horizontal";
        }
        else {
            $orientation = getOrientation($path);
        }
        
        $result = updateData("INSERT INTO `image`(uri, orientation, media_type) VALUES (?, ?, ?);", [$path, $orientation, $mediaType]);
        if (!is_numeric($result)) {
            return false;
        }
        array_push($insertIds, $result);
    }

    return $insertIds;
}

// A A termékképek és a termék összekapcsolása `product_image` táblába való feltöltéssel
function connectProductImages($insertIds, $productId) {
    foreach ($insertIds as $image) {
        $result = updateData("INSERT INTO `product_image`(image_id, product_id) VALUES (?, ?);", [$image, $productId]);

        if (!is_numeric($result)) {
            return false;
        }
    }
    return true;
}

// Termék létrehozása - Fő függvény
function createProduct($productData, $productPageData, $productCategoryData) {
    include_once "init.php";

    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $paths = createProductDirectory($productData);
    if (!is_array($paths)) {
        return "Sikertelen képfeltöltés. ($paths)";
    }

    $result = uploadProductData($productData);
    if (is_numeric($result)) {
        $productData["id"] = $result;
        $productPageData["product_id"] = $result;
    }
    else {
        return "Sikertelen feltöltés a product táblába. ($result)";
    }
    
    $result = getLinkSlug($productData['name'], $productCategoryData);
    if (is_array($result)) {
        $productPageData["link_slug"] = $result["link_slug"];
    }
    
    $result = uploadProductPageData($productPageData);
    if (!is_numeric($result)) {
        return "Sikertelen feltöltés a product_page táblába. ($result)";
    }

    $insertIds = uploadProductImages($paths);
    if (!is_array($insertIds)) {
        return "Sikertelen feltöltés az image táblába. ($insertIds)";
    }
    
    $result = connectProductImages($insertIds, $productData['id']);
    if ($result !== true) {
        return "Sikertelen feltöltés a product_image táblába. ($result)";
    }

    return true;
}


/* ----------------------------- Termék törlése ----------------------------- */

// Termék törlése az adatbázisból
function removeProductFromDB($productData) {
    $successfulDelete = updateData("DELETE `image` FROM `image` INNER JOIN product_image ON image.id=product_image.image_id WHERE product_image.product_id=?;", $productData['id']);
    if ($successfulDelete !== true) return $successfulDelete;
    
    return updateData("DELETE FROM product WHERE product.id = ?;", $productData['id']);
}

// Termék mappájának törlése
function removeProductDirectory($productData) {

    $baseDir = "./images/products/";
    $productName = format_str($productData['name']);
    $productDirURI = $baseDir.$productName."/";

    return deleteFolder($productDirURI);
}

// Termék törlése - Fő függvény
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

?>