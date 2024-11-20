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

    $result = connectProductTags($productData['id'], $productData['tags']);
    if (!is_numeric($result)) {
        return "Sikertelen feltöltés a product_tag táblába. ($result)";
    }

    return createProductPage($productData, $productPageData, $productCategoryData);
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

/* ---------------------------- Termék módosítása --------------------------- */

function updateProductTags($productData) {
    
    $query = "DELETE FROM product_tag WHERE product_tag.product_id=?;";
    $result = updateData($query, $productData["id"]);
    if (!is_bool($result)) {
        return "Sikertelen törlés.";
    }
    
    if (!isset($productData["tags"])) return true;

    $values = array();
    $placeholders = array();
    
    foreach ($productData["tags"] as $tag) {
        array_push($placeholders, "(?, ?)");
        array_push($values, $productData["id"]);
        array_push($values, $tag);
    }
    
    $query = "INSERT INTO product_tag (product_id, tag_id) VALUES " . implode(", ", $placeholders);
    $result = updateData($query, $values);
    if ($result !== true && !is_numeric($result)) {
        var_dump($result);
        return "Sikertelen felvitel.";
    }

    return true;
}

function updateProductImages($productData, $images, $imageIds, $paths) {
    // 1: Képek (csak amelyik típusból feltöltött) törlése az image táblából (kaszkádol)
    // 2: Elérési útvonalak feltöltése az image táblába
    // 3: connectProductImages függvény használata a képek és a termékek összekötésére

    if (count($paths) > 0 && count($imageIds) > 0) {
        $deleteIds = array();
    
        for ($i = 0; $i < count($imageIds); $i++){
            $name = $images[$i]["name"];
            $file = end(explode("/", $paths[$i]));
    
            if ( $name == "product_image" && str_contains($file, "image") ) {
                array_push($deleteIds, $imageIds[$i]);
            }
            else if ( str_contains($file, "thumbnail") ) {
                array_push($deleteIds, $imageIds[$i]);
            }
        }
    
        $query = "DELETE FROM images WHERE id IN (" . implode(',', array_fill(0, count($deleteIds), '?')) . ")";
        $result = updateData($query, $deleteIds);
        if ($result !== true) {
            return "Sikertelen törlés a product_images táblában.";
        }
    }

    return true;

}

function updateProductData($productData, $images, $imageIds, $paths) {
    
    $fields = array("name", "unit_price", "stock", "description");
    $values = array(
        $productData["name"],
        $productData["price"],
        $productData["stock"],
        $productData["description"],
        $productData["id"]
    );
    
    $query = "UPDATE `product` SET ";
    for ($i = 0; $i < count($values) - 1; $i++){
        $query .= "`{$fields[$i]}`=?";
        if ($i != count($values) - 2) $query .= ", ";
    }
    $query .= " WHERE `product`.`id`=?;";
    $result = updateData($query, $values);
    if ($result !== true) {
        return "Sikertelen frissítés.";
    }
    
    $result = updateProductTags($productData);
    if ($result !== true) {
        return $result;
    }

    return true;
    // $result = updateProductImages($productData, $images, $imageIds, $paths);
}

function renameProductDirectory($productData) {
    $baseDirectory = './images/products/';

    $productName = format_str($productData["name"]);
    $originalProductName = format_str($productData["original_name"]);

    $originalProductDirURI = $baseDirectory.$originalProductName."/";
    $productDirURI = $baseDirectory.$productName."/";
    
    if ($productName == $originalProductName) {
        return [ "message" => $originalProductDirURI, "type" => "NO_CHANGE" ];
    }

    if (renameFolder($originalProductDirURI, $productDirURI)) {
        
        $query = "SELECT image.id, image.uri FROM product_image INNER JOIN image ON product_image.image_id=image.id WHERE product_image.product_id = ?;";
        $result = selectData($query, $productData["id"]);

        if (!is_array($result)) {
            if ($result == "Nincs találat!") {
                return [ "message" => "Nem találhatóak a keresett termékhez képek.", "type" => "EMPTY" ];
            }
            else {
                return [ "message" => $result, "type" => "ERROR" ];
            }
        }

        $ids = array_map(function ($e) { return $e["id"]; }, $result);
        $uris = array_map(function ($e) { return $e["uri"]; }, $result);

        for ($i = 0; $i < count($uris); $i++) {
            $original_name = format_str($productData["original_name"]);
            $name = format_str($productData["name"]);
            
            // Az elérési útvonalban kicseréljük a régi terméknevet az újra egy speciális karakter segítségével ( | ).
            $uris[$i] = ($uris[$i] == "") ? null : str_replace('|', $name, str_replace($original_name, '|', $uris[$i]));
        }

        $caseStatements = [];
        foreach ($ids as $id) {
            $caseStatements[] = "WHEN id = {$id} THEN ?";
        }

        $caseSql = implode(' ', $caseStatements);
        $values = implode(', ', $ids);
    
        $query = "UPDATE image SET uri = CASE {$caseSql} END WHERE id IN (" . $values . ");";

        $result = updateData($query, $uris);
        if ($result !== true) {
            return [ "message" => "Sikertelen módosítás.", "type" => "ERROR" ];
        }

        return [ "message" => [$productDirURI, $ids], "type" => "SUCCESS" ];
    }
    else {
        return [ "message" => "Sikertelen mappa átnevezés.", "type" => "ERROR" ];
    }
}

function updateProductDirectory($productData, $images) {

    $result = renameProductDirectory($productData);

    if (!isSuccess($result)) {
        return $result["message"];
    }

    [$productDirURI, $ids] = $result["message"];

    if (!is_dir($productDirURI."thumbnail/") || !is_dir($productDirURI."gallery/")) {
        return "Hiányzó mappa.";
    }
    
    $thumbnailImages = array('thumbnail_image');
    if (isset($_FILES['product_video'])) array_push($thumbnailImages, 'product_video');
    
    $paths = array();
    $galleryCounter = 0;

    foreach ($images as $image) {
        if ($image["name"] != "product_image") {
            $dir = $productDirURI."thumbnail/";
            $tmp = $image["tmp_name"];
            $name = "thumbnail";
            $ext = $image["ext"];
            $path = $dir."$name.".$ext;

            $files = scandir($dir);

            $existingImage = null;
            foreach ($files as $file) {
                $path = $dir.$file;
                if (pathinfo($path, PATHINFO_FILENAME) == $name) {
                    $existingImage = $path;
                }
            }
    
            if ($existingImage) {
                array_push($paths, replaceFile($existingImage, $tmp, "$name.$ext", $name));
            }
            else {
                array_push($paths, moveFile($tmp, "$name.$ext", $name, $dir));
            }
        }
        else {
            $dir = $productDirURI."gallery/";
            $tmp = $image["tmp_name"];
            $name = "image$galleryCounter";
            $ext = $image["ext"];
            $path = $dir."$name.".$ext;
            
            $files = scandir($dir);
            
            $existingImage = null;
            foreach ($files as $file) {
                $path = $dir.$file;
                if (pathinfo($path, PATHINFO_FILENAME) == $name) {
                    $existingImage = $path;
                }
            }
            
            if ($existingImage) {
                array_push($paths, replaceFile($existingImage, $tmp, "$name.$ext", $name));
            }
            else {
                array_push($paths, moveFile($tmp, "$name.$ext", $name, $dir));
            }

            $galleryCounter++;
        }

    }
    return [$paths, $ids];
}

function updateProduct($productData) {
    include_once "init.php";

    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $images = array();
    $paths = array();

    if (isset($_FILES["thumbnail_image"])) {
        array_push($images, array("name" => "thumbnail_image", "tmp_name" => $_FILES["thumbnail_image"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_image"]["name"], PATHINFO_EXTENSION)));
    }
    if (isset($_FILES["product_images"])) {
        $count = count($_FILES["product_images"]["name"]);

        for ($i = 0; $i < $count; $i++) {
            array_push($images, array("name" => "product_image", "tmp_name" => $_FILES["product_images"]["tmp_name"][$i], "ext" => pathinfo($_FILES["product_images"]["name"][$i], PATHINFO_EXTENSION)));
        }
    }
    if (isset($_FILES["product_video"])) {
        array_push($images, array("name" => "product_video", "tmp_name" => $_FILES["product_video"]["tmp_name"], "ext" => pathinfo($_FILES["product_video"]["name"], PATHINFO_EXTENSION)));
    }

    if (count($images) > 0) {
        $result = updateProductDirectory($productData, $images);
        if (!is_array($result)) return $result;
    
        [$paths, $imageIds] = $result;
        if (hasError($paths)) return false;
    
        for ($i = 0; $i < count($images); $i++) {
            $productData[$images[$i]["name"]] = $paths[$i];
        }
    }
    else {
        $result = renameProductDirectory($productData);

        if (isError($result)) {
            var_dump($result);
            return $result["message"];
        }

        if ($result["type"] == "NO_CHANGE") {
            $productDirURI = $result["message"]; 
            $imageIds = array();
        }
        else {
            [$productDirURI, $imageIds] = $result["message"];
        }
    }

    return updateProductData($productData, $images, $imageIds, $paths);
}
?>