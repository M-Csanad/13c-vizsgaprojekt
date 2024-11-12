<?php
/* --------------------------- Termék létrehozása --------------------------- */

// Termékképek számára mappa létrehozása
function createProductDirectory($productData) {

    // A képek végső elérési útvonalait gyűjtjük
    $paths = array();
    $baseDirectory = './images/products/';

    $productName = format_str($productData['name']);
    $productDirURI = $baseDirectory.$productName."/";
    
    if (!createDirectory([$productDirURI,$productDirURI.'thumbnail/', $productDirURI.'gallery/'])) {
        return "Ilyen nevű termék már létezik a fájlrendszerben.";
    }
    
    // A termékképek feltöltése
    $thumbnailImages = array('thumbnail_image');
    if (isset($_FILES['product_video'])) array_push($thumbnailImages, 'product_video');

    foreach ($thumbnailImages as $image) {
        $tmp = $_FILES[$image]['tmp_name'];
        $name = $_FILES[$image]['name'];
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $path = $productDirURI."thumbnail/thumbnail." . $extension;

        if (!move_uploaded_file($tmp, $path)) {
            return false;
        }
    
        array_push($paths, $path);
    }
    
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

function connectProductTags($id, $tags) {
    $placeholderList = implode(", ", array_fill(0, count($tags), "(?, ?)"));;
    $values = array();

    foreach ($tags as $tag) {
        array_push($values, $tag, $id);
    }

    return updateData("INSERT INTO product_tag (tag_id, product_id) VALUES $placeholderList;", $values);
}

// Termék létrehozása - Fő függvény
function createProduct($productData, $tags) {
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
        if ($result->getCode() === 1062) {
            return "Ilyen termék már létezik az adatbázisban.";    
        }
        return "Sikertelen feltöltés a product táblába. ($result)";
    }

    $insertIds = uploadProductImages($paths);
    if (!is_array($insertIds)) {
        return "Sikertelen feltöltés az image táblába. ($insertIds)";
    }
    
    $result = connectProductImages($insertIds, $productData['id']);
    if ($result !== true) {
        return "Sikertelen feltöltés a product_image táblába. ($result)";
    }

    $result = connectProductTags($productData['id'], $tags);
    if (!is_numeric($result)) {
        return "Sikertelen feltöltés a product_tag táblába. ($result)";
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