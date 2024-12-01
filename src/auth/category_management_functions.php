<?php

function getCategoryDir($categoryData) {
    $baseDir = "./images/";

    if ($categoryData["type"] == "subcategory") {
        $baseDir .= "sub";
    }

    $baseDir .= "categories/";
    $dirName = "category-".$categoryData["id"]."/";
    return $baseDir.$dirName;
}

function createCategory($categoryData) {
    include_once "init.php";

    if (hasUploadError()) {
        return ["message" => "Hiba merült fel a feltöltés során.", "type" => "ERROR"];
    }

    $images = array();
    if (isset($_FILES["thumbnail_image_vertical"])) array_push($images, "thumbnail_image_vertical");
    if (isset($_FILES["thumbnail_image_horizontal"])) array_push($images, "thumbnail_image_horizontal");
    if (isset($_FILES["thumbnail_video"])) array_push($images, "thumbnail_video");
    
    $result = uploadCategoryData($categoryData);
    if (!typeOf($result, "SUCCESS")) {
        return $result;
    }

    // Get the generated ID
    $categoryID = $result["message"]; // Assuming uploadCategoryData returns an "id" in success response
    $categoryData["id"] = $categoryID;

    $result = createCategoryDirectory($categoryData, $images);

    if (typeOf($result, "ERROR")) {
        return $result;
    }
    
    $paths = $result["message"];
    if (hasError($paths)) return ["message" => "Hiba a fájlok mozgatásakor.", "type" => "ERROR"];

    // Add file paths to categoryData
    foreach ($images as $i => $image) {
        $categoryData[$image] = $paths[$i];
        optimizeImage($paths[$i]);
    }

    $result = uploadCategoryImages($categoryData);
    if (typeOf($result, "ERROR")) {
        return $result;
    }

    return ["message" => "Kategória sikeresen létrehozva!", "type" => "SUCCESS"];
}


function createCategoryDirectory($categoryData, $images) {
    // Use the database ID to create the directory
    $categoryDirURI = getCategoryDir($categoryData);

    if (!createDirectory($categoryDirURI)) {
        return ["message" => "A mappa létrehozása sikertelen.", "type" => "ERROR"];
    }

    $paths = array();

    foreach ($images as $name) {
        $newPath = moveFile($_FILES[$name]["tmp_name"], $_FILES[$name]["name"], $name, $categoryDirURI);

        if ($newPath !== false) {
            array_push($paths, $newPath);
        } else if ($name == "thumbnail_video") {
            array_push($paths, null);
        } else {
            return ["message" => "Hiba a fájl mozgatásakor ($name).", "type" => "ERROR"];
        }
    }

    return ["message" => $paths, "type" => "SUCCESS"];
}


function uploadCategoryData($categoryData) {
    $table = $categoryData["type"];
    $isMainCategory = $table == "category";
    
    $fields = array("name", "subname", "description");
    $values = array(
        $categoryData["name"],
        $categoryData["subname"],
        $categoryData["description"]
    );
    $types = "sss";
    
    if (!$isMainCategory) {
        array_push($fields, "category_id");
        array_push($values, $categoryData["parent_category_id"]);
        $types .= "i";
    }
    
    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `$table`($fieldList) VALUES ($placeholderList);";
    
    return updateData($query, $values, $types);
}

function uploadCategoryImages($categoryData) {
    $table = $categoryData["type"];
    $types = array('thumbnail_image_vertical', 'thumbnail_image_horizontal', 'thumbnail_video');
    
    $fields = array();
    $values = array();
    $typeString = "";

    foreach ($types as $type) {
        if (isset($categoryData[$type])) {
            array_push($values, $categoryData[$type]);
            array_push($fields, $type."_uri");
            $typeString .= "s";
        }
    }

    $query = "UPDATE $table SET ";

    foreach ($fields as $i => $field) {
        $query .= $field."=?";
        if ($i != count($fields) - 1) {
            $query .= ", ";
        }
    }

    $query .= " WHERE id=?;";
    $typeString .= "i";
    return updateData($query, [...$values, $categoryData["id"]], $typeString);
}


function removeCategory($categoryData) {
    include_once "init.php";

    // A kategória törlése az adatbázisból
    $result = removeCategoryFromDB($categoryData);
    if (typeOf($result, "ERROR")) {
        return $result;
    }
    else if (typeOf($result, "NO_AFFECT")) {
        return ["message" => "A törlendő kategória nem létezik az adatbázisban!", "type" => "ERROR"];
    }

    // A kategória mappájának törlése
    $result = removeCategoryDirectory($categoryData);
    if (!$result) {
        return ["message" => "A mappa törlése sikertelen volt!.", "type" => "ERROR"];
    }
    else {
        return ["message" => $result, "type" => "SUCCESS"];
    }
}

function removeCategoryFromDB($categoryData) {
    $query = "DELETE FROM {$categoryData["type"]} WHERE {$categoryData["type"]}.id = ?;";
    return updateData($query, $categoryData["id"], "i");
}

function removeCategoryDirectory($categoryData) {
    $dir = getCategoryDir($categoryData);
    return deleteFolder($dir);
}

function updateCategoryDirectory($categoryData, $images) {

    $categoryDirURI = getCategoryDir($categoryData);
    $paths = array();

    foreach ($images as $image) {
        $name = $image["name"];
        $tmp = $image["tmp_name"];
        $ext = $image["ext"];
        
        $files = scandir($categoryDirURI);

        $existingImage = null;
        foreach ($files as $file) {
            $path = $categoryDirURI.$file;
            if (pathinfo($path, PATHINFO_FILENAME) == $name) {
                $existingImage = $path;
            }
        }

        if ($existingImage) {
            array_push($paths, replaceFile($existingImage, $tmp, "$name.$ext", $name));
        }
        else {
            array_push($paths, moveFile($tmp, "$name.$ext", $name, $categoryDirURI));
        }
    }

    return ["message" => $paths, "type" => "SUCCESS"];
}

function updateCategoryData($categoryData, $images) {
    
    $table = $categoryData["type"];
    $isMainCategory = $table == "category";
    
    $fields = array("name", "subname", "description");
    $values = array(
        $categoryData["name"],
        $categoryData["subname"],
        $categoryData["description"],
    );
    $typeString = "sss";

    foreach ($images as $image) {
        array_push($fields, $image["name"]."_uri");
        array_push($values, $categoryData[$image["name"]]);
        $typeString .= "s";
    }
    
    if (!$isMainCategory) {
        array_push($fields, "category_id");
        array_push($values, $categoryData["parent_category_id"]);
        $typeString .= "i";
    }

    array_push($values, $categoryData["id"]);
    $typeString .= "i";

    $query = "UPDATE `$table` SET ";
    for ($i = 0; $i < count($values) - 1; $i++){
        $query .= "`{$fields[$i]}`=?";
        if ($i != count($values) - 2) $query .= ", ";
    }
    $query .= " WHERE `$table`.`id`=?;";

    return updateData($query, $values, $typeString);
}

function updateURIs($categoryData) {
    if (isset($categoryData["thumbnail_image_vertical"])) {
        $result = updateData("UPDATE {$categoryData['type']} SET thumbnail_image_vertical_uri=? WHERE id=?;", [$categoryData['thumbnail_image_vertical'], $categoryData['id']], "si");
        if (typeOf($result, "ERROR")) return $result;
    }
    
    if (isset($categoryData["thumbnail_image_horizontal"])) {
        $result = updateData("UPDATE {$categoryData['type']} SET thumbnail_image_horizontal_uri=? WHERE id=?;", [$categoryData['thumbnail_image_horizontal'], $categoryData['id']], "si");
        if (typeOf($result, "ERROR")) return $result;
    }

    if (isset($categoryData["thumbnail_video"])) {
        $result = updateData("UPDATE {$categoryData['type']} SET thumbnail_video_uri=? WHERE id=?;", [$categoryData['thumbnail_video'], $categoryData['id']], "si");
        if (typeOf($result, "ERROR")) return $result;
    }
    return $result;
}

function updateCategory($categoryData) {
    include_once "init.php";

    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $images = array();

    if (isset($_FILES["thumbnail_image_vertical"])) {
        array_push($images, array("name" => "thumbnail_image_vertical", "tmp_name" => $_FILES["thumbnail_image_vertical"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_image_vertical"]["name"], PATHINFO_EXTENSION)));
    }
    if (isset($_FILES["thumbnail_image_horizontal"])) {
        array_push($images, array("name" => "thumbnail_image_horizontal", "tmp_name" => $_FILES["thumbnail_image_horizontal"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_image_horizontal"]["name"], PATHINFO_EXTENSION)));
    }
    if (isset($_FILES["thumbnail_video"])) {
        array_push($images, array("name" => "thumbnail_video", "tmp_name" => $_FILES["thumbnail_video"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_video"]["name"], PATHINFO_EXTENSION)));
    }
    
    if (count($images) > 0) {
        $result = updateCategoryDirectory($categoryData, $images);
        
        if (typeOf($result, "ERROR")) {
            return $result;
        }
        
        $paths = $result["message"];
        if (hasError($paths)) {
            return ["message" => "A képek mozgatásakor hiba merült fel.", "type" => "ERROR"];
        }
         
        for ($i = 0; $i < count($images); $i++) {
            $categoryData[$images[$i]["name"]] = $paths[$i];
            optimizeImage($paths[$i]);
        }

        $result = updateURIs($categoryData);
        if (typeOf($result, "ERROR")) {
            return $result;
        }
    }

    $result = updateCategoryData($categoryData, $images);
    if (!typeOf($result, "SUCCESS")) {
        return $result;
    }

    return updateProductPage($categoryData, $categoryData["type"]);
}