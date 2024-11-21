<?php

function createCategory($categoryData) {
    include_once "init.php";

    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return ["message" => "Hiba merült fel a feltöltés során.", "type" => "ERROR"];
    }

    $categoryType = $categoryData["type"];
    unset($categoryData["type"]);

    $images = array("thumbnail_image_vertical", "thumbnail_image_horizontal");
    if (isset($_FILES["thumbnail_video"])) array_push($images, "thumbnail_video");
    
    $result = createCategoryDirectory($categoryData, $categoryType, $images);

    if (typeOf($result, "ERROR")) {
        return $result;
    }
    
    $paths = $result["message"];
    if (hasError($paths)) return ["message" => "Hiba a fájlok mozgatásakor.", "type" => "ERROR"];

    for ($i = 0; $i < count($images); $i++) {
        $categoryData[$images[$i]] = $paths[$i];
    }
    
    return uploadCategoryData($categoryData, $categoryType);
}

function createCategoryDirectory($categoryData, $categoryType, $images) {
    $baseDirectory = "./images/categories/".($categoryType === "sub" ? format_str($categoryData["parent_category"]) . "/" : "");
    $categoryDirURI = $baseDirectory . format_str($categoryData["name"])."/";
    
    if (!createDirectory($categoryDirURI)) {
        return ["message" => "Ilyen nevű kategória már létezik.", "type" => "ERROR"];
    }

    $paths = array();

    foreach ($images as $name) {
        $newPath = moveFile($_FILES[$name]["tmp_name"], $_FILES[$name]["name"], $name, $categoryDirURI);

        if ($newPath !== false) {
            array_push($paths, $newPath);
        }
        else if ($name == "thumbnail_video"){
            array_push($paths, null);
        }
        else {
            return ["message" => "Hiba a fájl mozgatásakor ($name).", "type" => "ERROR"];
        }
    }

    return ["message" => $paths, "type" => "SUCCESS"];
}

function uploadCategoryData($categoryData, $categoryType) {
    $isMainCategory = ($categoryType == "main");
    $table = $isMainCategory ? "category" : "subcategory";
    
    $fields = array("name", "subname", "description", "thumbnail_image_horizontal_uri", "thumbnail_image_vertical_uri");
    $values = array(
        $categoryData["name"],
        $categoryData["subname"],
        $categoryData["description"],
        $categoryData["thumbnail_image_horizontal"],
        $categoryData["thumbnail_image_vertical"]
    );
    
    if (!empty($categoryData["thumbnail_video"])) {
        array_push($fields, "thumbnail_video_uri");
        array_push($values, $categoryData["thumbnail_video"]);
    }
    
    if (!$isMainCategory) {
        array_push($fields, "category_id");
        array_push($values, $categoryData["parent_category_id"]);
    }
    
    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `$table`($fieldList) VALUES ($placeholderList);";
    
    return updateData($query, $values);
}

function removeCategory($categoryData) {
    include_once "init.php";

    // Az alkategóriához tartozó főkategória lekérdezése
    if ($categoryData["type"] == "subcategory") {
        $result = selectData("SELECT category.name 
                                FROM subcategory 
                                INNER JOIN category 
                                ON subcategory.category_id = category.id 
                                WHERE subcategory.id = ?", $categoryData["id"]);

        // Ha nincs főkategóriája, akkor nincs olyan alkategória, mivel kötelező a category_id
        if (typeOf($result, "ERROR")) {
            return $result;
        }
        else if (typeOf($result, "EMPTY")) {
            return ["message" => "Ez a kategória nem létezik az adatbázisban!", "type" => "ERROR"];
        }

        $categoryData["parent_category"] = format_str($result["message"]["name"]);
    }

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
    return updateData($query, $categoryData["id"]);
}

function removeCategoryDirectory($categoryData) {
    $baseDir = "./images/categories/";
    $categoryName = format_str($categoryData["name"]);

    if ($categoryData["type"] == "subcategory") {
        $baseDir .= $categoryData["parent_category"]."/";
    }

    $categoryDirURI = $baseDir.$categoryName."/";

    return deleteFolder($categoryDirURI);
}

function renameCategoryDirectory($categoryData, $categoryType) {

    $name = $categoryData["name"];
    $originalName = $categoryData["original_name"];
    $categoryName = format_str($name);
    $originalCategoryName = format_str($originalName);
    
    $baseDirectory = "./images/categories/";
    $originalBaseDir = $baseDirectory;

    if (isset($_POST["parent_category"])) {
        $parentName = format_str($categoryData["parent_category"]);
        $originalParentName = format_str($categoryData["original_parent_category"]);
        $baseDirectory .= $parentName."/";
        $originalBaseDir .= $originalParentName."/";
    }

    $originalCategoryDirURI = $originalBaseDir.$originalCategoryName."/";
    $categoryDirURI = $baseDirectory.$categoryName."/";
    
    if ($categoryDirURI == $originalCategoryDirURI) {
        return ["message" => $originalCategoryDirURI, "type" => "SUCCESS"];
    }
    
    
    $table = ($categoryType == "main" ? "category" : "subcategory");
    if ($table == "category") {
        $operation = renameFolder($originalCategoryDirURI, $categoryDirURI);
    }
    else {
        $operation = moveFolder($originalCategoryDirURI, $categoryDirURI);
    }
    if ($operation) {
        
        if ($table == "category") {
            $query = "SELECT category.thumbnail_image_vertical_uri AS 'category_vertical', category.thumbnail_image_horizontal_uri AS 'category_horizontal', category.thumbnail_video_uri AS 'category_video',
                      subcategory.thumbnail_image_vertical_uri AS 'subcategory_vertical', subcategory.thumbnail_image_horizontal_uri AS 'subcategory_horizontal', subcategory.thumbnail_video_uri AS 'subcategory_video' 
                      FROM category LEFT JOIN subcategory ON subcategory.category_id=category.id WHERE category.id=?";
        }
        else {
            $query = "SELECT subcategory.thumbnail_image_vertical_uri, subcategory.thumbnail_image_horizontal_uri, subcategory.thumbnail_video_uri 
                      FROM subcategory WHERE subcategory.id=?";
        }

        $result = selectData($query, $categoryData["id"]);

        if (!typeOf($result, "SUCCESS")) {
            return $result;
        }
        
        $uris = array_values($result["message"]);
        for ($i = 0; $i < count($uris); $i++) {
            
            // Az elérési útvonalban kicseréljük a régi mappanevet az újra egy speciális karakter segítségével ( | ).
            $uris[$i] = ($uris[$i] == "") ? null : str_replace('|', format_str($name), str_replace(format_str($originalName), '|', $uris[$i]));
            if ($table == "subcategory") {
                $uris[$i] = ($uris[$i] == "") ? null : str_replace('|', format_str($parentName), str_replace(format_str($originalParentName), '|', $uris[$i]));
            }
        }

        $query = "UPDATE $table SET $table.thumbnail_image_vertical_uri=?, $table.thumbnail_image_horizontal_uri=?, $table.thumbnail_video_uri=? WHERE $table.id=?";

        if ($table == "category") {

            $values = [...array_slice($uris, 0, 3), $categoryData["id"]];
            $result = updateData($query, $values);
            if (typeOf($result, "ERROR")) {
                return $result;
            }


            $result = updateData("UPDATE subcategory SET subcategory.thumbnail_image_vertical_uri=?, 
                        subcategory.thumbnail_image_horizontal_uri=?, subcategory.thumbnail_video_uri=? 
                        WHERE subcategory.category_id=?", [...array_slice($uris, 3), $categoryData["id"]]);
            if (typeOf($result, "ERROR")) {
                return $result;
            }         
        }
        else {
            $values = [...$uris, $categoryData["id"]];
            $result = updateData($query, $values);
            if (typeOf($result, "ERROR")) {
                return $result;
            }
        }

        return ["message" => $categoryDirURI, "type" => "SUCCESS"];
    }
    else {
        return ["message" => "A mappa átnevezése / áthelyezése nem sikerült.", "type" => "ERROR"];
    }
}

function updateCategoryDirectory($categoryData, $categoryType, $images) {

    $result = renameCategoryDirectory($categoryData, $categoryType);

    if (typeOf($result, "ERROR")) {
        return ["message" => "Sikertelen mappa átnevezés.", "type" => "ERROR"];
    }
    
    $categoryDirURI = $result["message"];
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

function updateCategoryData($categoryData, $categoryType, $images) {
    
    $isMainCategory = ($categoryType == "main");
    $table = $isMainCategory ? "category" : "subcategory";
    
    $fields = array("name", "subname", "description");
    $values = array(
        $categoryData["name"],
        $categoryData["subname"],
        $categoryData["description"],
    );

    foreach ($images as $image) {
        array_push($fields, $image["name"]."_uri");
        array_push($values, $categoryData[$image["name"]]);
    }

    
    if (!$isMainCategory) {
        array_push($fields, "category_id");
        array_push($values, $categoryData["parent_category_id"]);
    }

    array_push($values, $categoryData["id"]);

    $query = "UPDATE `$table` SET ";
    for ($i = 0; $i < count($values) - 1; $i++){
        $query .= "`{$fields[$i]}`=?";
        if ($i != count($values) - 2) $query .= ", ";
    }
    $query .= " WHERE `$table`.`id`=?;";

    return updateData($query, $values);
}

function updateCategory($categoryData) {
    include_once "init.php";

    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $categoryType = isset($categoryData["parent_category"]) ? "sub" : "main";

    $modifiedColumns = array();
    $images = array();

    if (isset($_FILES["thumbnail_image_vertical"])) {
        array_push($images, array("name" => "thumbnail_image_vertical", "tmp_name" => $_FILES["thumbnail_image_vertical"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_image_vertical"]["name"], PATHINFO_EXTENSION)));
        array_push($modifiedColumns, "thumbnail_image_vertical");
    }
    if (isset($_FILES["thumbnail_image_horizontal"])) {
        array_push($images, array("name" => "thumbnail_image_horizontal", "tmp_name" => $_FILES["thumbnail_image_horizontal"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_image_horizontal"]["name"], PATHINFO_EXTENSION)));
        array_push($modifiedColumns, "thumbnail_image_horizontal");
    }
    if (isset($_FILES["thumbnail_video"])) {
        array_push($images, array("name" => "thumbnail_video", "tmp_name" => $_FILES["thumbnail_video"]["tmp_name"], "ext" => pathinfo($_FILES["thumbnail_video"]["name"], PATHINFO_EXTENSION)));
        array_push($modifiedColumns, "thumbnail_video");
    }
    
    if (count($images) > 0) {
        $result = updateCategoryDirectory($categoryData, $categoryType, $images);
    
        if (typeOf($result, "ERROR")) {
            return $result;
        }
        
        $paths = $result["message"];
        if (hasError($paths)) {
            return ["message" => "A képek mozgatásakor hiba merült fel.", "type" => "ERROR"];
        }
        
        for ($i = 0; $i < count($images); $i++) {
            $categoryData[$images[$i]["name"]] = $paths[$i];
        }
    }
    else {
        $result = renameCategoryDirectory($categoryData, $categoryType);

        if (typeOf($result, "ERROR")) {
            return ["message" => "Sikertelen mappa átnevezés. ({$result["message"]})", "type" => "ERROR"];
        }
    }

    return updateCategoryData($categoryData, $categoryType, $images);
}