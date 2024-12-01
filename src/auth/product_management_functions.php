<?php
/* --------------------------- Termék létrehozása --------------------------- */

function getProductDir($productData) {
    $baseDir = "./images/products/";
    $dirName = "product-".$productData["id"]."/";
    return $baseDir.$dirName;
}

function createProductDirectory($productData) {
    $paths = [];
    $productDirURI = getProductDir($productData);

    if (!createDirectory($productDirURI)) {
        return ["message" => "Ilyen nevű termék már létezik a fájlrendszerben.", "type" => "ERROR"];
    }

    $images = ['thumbnail_image'];
    if (isset($_FILES['product_video'])) {
        $images[] = 'product_video';
    }

    foreach ($images as $image) {
        if (isset($_FILES[$image])) {
            $path = saveFile($_FILES[$image], $productDirURI, "thumbnail");
            if (!$path) return ["message" => "Sikertelen kép mentés.", "type" => "ERROR"];
            $paths[] = $path;
        }
    }

    if (!empty($_FILES['product_images']['name'])) {
        foreach ($_FILES['product_images']['name'] as $index => $name) {
            $path = saveFile(
                [
                    'tmp_name' => $_FILES['product_images']['tmp_name'][$index],
                    'name' => $name
                ],
                $productDirURI,
                "image{$index}"
            );
            if (!$path) return ["message" => "Sikertelen kép mentés.", "type" => "ERROR"];
            $paths[] = $path;
        }
    }

    return ["message" => $paths, "type" => "SUCCESS"];
}

function saveFile($file, $directory, $filename) {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $path = $directory . $filename . "." . $extension;
    return move_uploaded_file($file['tmp_name'], $path) ? $path : false;
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
    
    return updateData($query, $values, "siis");
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
        
        $result = updateData("INSERT INTO `image`(uri, orientation, media_type) VALUES (?, ?, ?);", [$path, $orientation, $mediaType], "sss");
        if (!typeOf($result, "SUCCESS")) {
            return $result;
        }

        array_push($insertIds, $result["message"]);
    }

    return ["message" => $insertIds, "type" => "SUCCESS"];
}

// A A termékképek és a termék összekapcsolása `product_image` táblába való feltöltéssel
function connectProductImages($insertIds, $productId) {
    foreach ($insertIds as $image) {
        $result = updateData("INSERT INTO `product_image`(image_id, product_id) VALUES (?, ?);", [$image, $productId], "ii");

        if (!typeOf($result, "SUCCESS")) {
            return $result;
        }
    }
    return ["message" => "A termékképek feltöltése sikeres volt.", "type" => "SUCCESS"];
}

function connectProductTags($id, $tags) {
    $placeholderList = implode(", ", array_fill(0, count($tags), "(?, ?)"));
    $values = array();
    $typeString = "";

    foreach ($tags as $tag) {
        array_push($values, $tag, $id);
        $typeString .= "ii";
    }

    return updateData("INSERT INTO product_tag (tag_id, product_id) VALUES $placeholderList;", $values, $typeString);
}

// Termék létrehozása - Fő függvény
function createProduct($productData, $productPageData, $productCategoryData) {
    include_once "init.php";

    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return ["message" => "Hiba merült fel a feltöltés során.", "type" => "ERROR"];
    }
    
    $result = uploadProductData($productData);
    if (typeOf($result, "SUCCESS")) {
        $productData["id"] = $result["message"];
        $productPageData["product_id"] = $result["message"];
    }
    else if (isError($result)){
        if ($result["code"] === 1062) {
            return ["message" => "Ilyen termék már létezik az adatbázisban.", "type" => "ERROR"];    
        }
        else {
            return ["message" => "Sikertelen feltöltés a product táblába. ({$result["message"]})", "type" => "ERROR"];
        }
    }
    else {
        return ["message" => "Sikertelen feltöltés az adatbázisba: {$result['message']}", "type" => "ERROR"];
    }

    $result = createProductDirectory($productData);
    if (!typeOf($result, "SUCCESS")) {
        return ["message" => "Sikertelen képfeltöltés. ($result)", "type" => "ERROR"];
    }
    $paths = $result["message"];

    var_dump($paths);
    $result = uploadProductImages($paths);
    if (!typeOf($result, "SUCCESS")) {
        return ["message" => "Sikertelen feltöltés az image táblába. ({$result['message']})", "type" => "ERROR"];
    }
    $insertIds = $result["message"];

    $result = connectProductImages($insertIds, $productData['id']);
    if (!typeOf($result, "SUCCESS")) {
        return ["message" => "Sikertelen feltöltés a product_image táblába. ({$result["message"]})", "type" => "ERROR"];
    }

    $result = connectProductTags($productData['id'], $productData['tags']);
    if (!typeOf($result, "SUCCESS")) {
        return ["message" => "Sikertelen feltöltés a product_tag táblába. ({$result["message"]})", "type" => "ERROR"];
    }

    return createProductPage($productData, $productPageData, $productCategoryData);
}


/* ----------------------------- Termék törlése ----------------------------- */

// Termék törlése az adatbázisból
function removeProductFromDB($productData) {
    $result = updateData("DELETE `image` FROM `image` INNER JOIN product_image ON image.id=product_image.image_id WHERE product_image.product_id=?;", $productData['id'], "i");
    if (typeOf($result, "ERROR")) return $result;
    
    return updateData("DELETE FROM product WHERE product.id = ?;", $productData['id'], "i");
}

// Termék mappájának törlése
function removeProductDirectory($productData) {
    return deleteFolder(getProductDir($productData));
}

// Termék törlése - Fő függvény
function removeProduct($productData) {
    include_once 'init.php';

    // A kategória törlése az adatbázisból
    $result = removeProductFromDB($productData);
    if (typeOf($result, "NO_AFFECT")) return ["message" => "Ez a termék nem létezik!", "type" => "ERROR"];
    else if (typeOf($result, "ERROR")) return $result;

    // A kategória mappájának törlése
    $successfulDirectoryDelete = removeProductDirectory($productData);
    if (!$successfulDirectoryDelete) return ["message" => "A mappa törlése sikertelen! (A mappát manuálisan kell törölni).", "type" => "ERROR"];

    return ["message" => "A termék sikeresen törölve lett.", "type" => "SUCCESS"];
}

/* ---------------------------- Termék módosítása --------------------------- */

function updateProductTags($productData) {
    
    $query = "DELETE FROM product_tag WHERE product_tag.product_id=?;";
    $result = updateData($query, $productData["id"], "i");
    if (typeOf($result, "ERROR")) {
        return ["message" => "Sikertelen törlés.", "type" => "ERROR"];
    }

    if (!isset($productData["tags"])) return ["message" => "Nincs változás.", "type" => "SUCCESS"];
    
    $values = array();
    $placeholders = array();
    $typeString = "";
    
    foreach ($productData["tags"] as $tag) {
        array_push($placeholders, "(?, ?)");
        array_push($values, $productData["id"], $tag);
        $typeString .= "ii";
    }
    
    $query = "INSERT INTO product_tag (product_id, tag_id) VALUES " . implode(", ", $placeholders);
    $result = updateData($query, $values, $typeString);
    if (typeOf($result, "ERROR")) {
        return ["message" => "Sikertelen felvitel.", "type" => "ERROR"];
    }

    return ["message" => "Sikeres törlés.", "type" => "SUCCESS"];
}

function updateProductPage($data, $table = null) {
    if (!$table) return ["message" => "Hiányzó táblázat paraméter.", "type" => "ERROR"];

    $name = format_str($data["name"]);
    $originalName = format_str($data["original_name"]);

    if ($name == $originalName) {
        return ["message" => "A termékoldal nem változott.", "type" => "SUCCESS"];
    }

    $result = selectData("SELECT product_page.id, product_page.link_slug FROM product_page WHERE product_page.{$table}_id=?", $data["id"], "i");
    if (!typeOf($result, "SUCCESS")) {
        return ["message" => "Sikertelen lekérdezés: ".$result["message"], "type" => "ERROR"];
    }

    $pages = $result["message"];
    if ($result["contentType"] == "ASSOC") $pages = [$pages];
    
    $slugs = array_map(function ($e) {return $e["link_slug"];}, $pages);
    $ids = array_map(function ($e) {return $e["id"];}, $pages);
    $typeString = "";
    
    for ($i = 0; $i < count($pages); $i++) {
        $slug = $slugs[$i];
        $slugs[$i] = str_replace('|', $name, str_replace($originalName, '|', $slug));
    }

    $caseStatements = [];
    foreach ($ids as $id) {
        $caseStatements[] = "WHEN id = {$id} THEN ?";
        $typeString .= "s";
    }
    
    $caseSql = implode(' ', $caseStatements);
    $values = implode(', ', $ids);
    
    $query = "UPDATE product_page SET link_slug = CASE {$caseSql} END, page_title=? WHERE id IN (" . $values . ");";
    $result = updateData($query, [...$slugs, $data["name"]], $typeString."s");

    if (typeOf($result, "ERROR")) {
        return ["message" => "Sikertelen módosítás: ".$result['message'], "type" => "ERROR"];
    }

    return ["message" => "Sikeres módosítás.", "type" => "SUCCESS"];
}

function updateProductImages($productData, $images, $paths) {
    if (count($paths) == 0) return false;

    if (count(array_filter($images, function ($e) {return $e["name"]=="product_image";})) > 0) {
        $result = updateData("DELETE image FROM image INNER JOIN product_image ON image.id=product_image.image_id WHERE image.uri LIKE '%image%' AND product_image.product_id=?;", $productData["id"], "i");
        if (typeOf($result, "ERROR")) {
            return $result;
        }
    }
    if (count(array_filter($images, function ($e) {return $e["name"]=="thumbnail_image";})) > 0) {
        $result = updateData("DELETE image FROM image INNER JOIN product_image ON image.id=product_image.image_id WHERE image.uri LIKE '%thumbnail%' AND image.media_type='image' AND product_image.product_id=?;", $productData["id"], "i");
        if (typeOf($result, "ERROR")) {
            return $result;
        }
    }
    if (count(array_filter($images, function ($e) {return $e["name"]=="product_video";})) > 0) {
        $result = updateData("DELETE image FROM image INNER JOIN product_image ON image.id=product_image.image_id WHERE image.uri LIKE '%thumbnail%' AND image.media_type='video' AND product_image.product_id=?;", $productData["id"], "i");
        if (typeOf($result, "ERROR")) {
            return $result;
        }
    }
    $ids = array();
    for ($i = 0; $i < count($paths); $i++) {
        $image = $images[$i];
        $path = $paths[$i];

        if ($image["name"] == "thumbnail_image" || $image["name"] == "product_image") {

            $result = updateData("INSERT INTO image(uri, orientation, media_type) VALUES (?, ?, ?);", [$path, getOrientation($path), "image"], "sss");
            if (typeOf($result, "ERROR") || $result["contentType"] != "INT") {
                return $result;
            }

            array_push($ids, $result["message"]);
        }
        else if ($image["name"] == "product_video") {

            $result = updateData("INSERT INTO image(uri, orientation, media_type) VALUES (?, ?, ?);", [$path, "horizontal", "video"], "sss");
            if (typeOf($result, "ERROR") || $result["contentType"] != "INT") {
                return $result;
            }

            array_push($ids, $result["message"]);
        }
    }

    return connectProductImages($ids, $productData["id"]);
}

function updateProductData($productData, $images, $paths) {
    
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

    $result = updateData($query, $values, "siisi");
    if (typeOf($result, "ERROR")) {
        return ["message" => "Sikertelen frissítés.", "type" => "ERROR"];
    }

    $result = updateProductPage($productData, "product");
    if (!typeOf($result, "SUCCESS")) {
        return $result;
    }
    
    $result = updateProductTags($productData);
    if (!typeOf($result, "SUCCESS")) {
        return $result;
    }

    return updateProductImages($productData, $images, $paths);
}

function updateProductDirectory($productData, $images) {

    $productDirURI = getProductDir($productData);

    if (!is_dir($productDirURI)) {
        return ["message" => "Hiányzó mappa.", "type" => "ERROR"];
    }
    
    $thumbnailImages = array('thumbnail_image');
    if (isset($_FILES['product_video'])) array_push($thumbnailImages, 'product_video');
    
    $paths = array();
    $galleryCounter = 0;

    foreach ($images as $image) {
        $files = scandir($productDirURI);

        if ($image["name"] != "product_image") {
            $tmp = $image["tmp_name"];
            $name = "thumbnail";
            $ext = $image["ext"];
            $path = $productDirURI."$name.".$ext;

            $existingImage = null;
            foreach ($files as $file) {
                $path = $productDirURI.$file;
                if (pathinfo($path, PATHINFO_FILENAME) == $name && str_contains($image["name"], explode("/", mime_content_type($path))[0])) {
                    $existingImage = $path;
                }
            }
    
            if ($existingImage) {
                array_push($paths, replaceFile($existingImage, $tmp, "$name.$ext", $name));
            }
            else {
                array_push($paths, moveFile($tmp, "$name.$ext", $name, $productDirURI));
            }
        }
        else {
            $tmp = $image["tmp_name"];
            $name = "image$galleryCounter";
            $ext = $image["ext"];
            $path = $productDirURI."$name.".$ext;
            
            if ($galleryCounter == 0) {
                foreach ($files as $file) {
                    if (preg_match('/^image\d+\.\w+$/', $file)) {
                        unlink($productDirURI . $file);
                    }
                }
            }

            array_push($paths, moveFile($tmp, "$name.$ext", $name, $productDirURI));
            $galleryCounter++;
        }

    }
    return ["message" => $paths, "type" => "SUCCESS"];
}

function updateProduct($productData) {
    include_once "init.php";

    if (hasUploadError()) {
        return ["message" => "Hiba merült fel a feltöltés során.", "type" => "ERROR"];
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
        if (typeOf($result, "ERROR")) {
            return $result;
        }
        $paths = $result["message"];

        if (hasError($paths)) return ["message" => "A képek mozgatása sikertelen volt.", "type" => "ERROR"];
    
        for ($i = 0; $i < count($images); $i++) {
            $productData[$images[$i]["name"]] = $paths[$i];
        }
    }

    return updateProductData($productData, $images, $paths);
}