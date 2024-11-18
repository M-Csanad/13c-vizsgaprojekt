<?php

function createCategory($categoryData) {
    include_once "init.php";

    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $categoryType = $categoryData["type"];
    unset($categoryData["type"]);

    $images = array("thumbnail_image_vertical", "thumbnail_image_horizontal");
    if (isset($_FILES["thumbnail_video"])) array_push($images, "thumbnail_video");
    
    $paths = createCategoryDirectory($categoryData, $categoryType, $images);

    if (!is_array($paths)) return $paths;
    if (hasError($paths)) return false;

    for ($i = 0; $i < count($images); $i++) {
        $categoryData[$images[$i]] = $paths[$i];
    }
    
    return uploadCategoryData($categoryData, $categoryType);
}

function createCategoryDirectory($categoryData, $categoryType, $images) {
    $baseDirectory = "./images/categories/".($categoryType === "sub" ? format_str($categoryData["parent_category"]) . "/" : "");
    $categoryDirURI = $baseDirectory . format_str($categoryData["name"])."/";
    
    if (!createDirectory($categoryDirURI)) {
        return "Ilyen nevű kategória már létezik.";
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
            return false;
        }
    }

    return $paths;
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
        if ($result == "Nincs találat!") {
            return "Ez a kategória nem létezik!";
        }

        $categoryData["parent_category"] = format_str($result["name"]);
    }

    // A kategória törlése az adatbázisból
    $successfulDelete = removeCategoryFromDB($categoryData);
    if ($successfulDelete === false) return "Ez a kategória nem létezik!";

    else if ($successfulDelete !== true) return $successfulDelete;

    // A kategória mappájának törlése
    $successfulDirectoryDelete = removeCategoryDirectory($categoryData);
    if (!$successfulDirectoryDelete) return "A mappa törlése sikertelen! (A mappát manuálisan kell törölni).";

    return $successfulDirectoryDelete;
}

function removeCategoryFromDB($categoryData) {
    
    $query = "DELETE FROM x WHERE x.id = ?;";
    $query = str_replace("x", $categoryData["type"], $query);
    $successfulDelete = updateData($query, $categoryData["id"]);

    return $successfulDelete;
}

function removeCategoryDirectory($categoryData) {
    $baseDir = "./images/categories/";
    $categoryName = format_str($categoryData["name"]);

    if ($categoryData["type"] == "subcategory") {
        $baseDir .= $categoryData["parent_category"]."/";
    }

    $categoryDirURI = $baseDir.$categoryName."/";

    $successfulDelete = deleteFolder($categoryDirURI);

    return $successfulDelete;
}

function renameCategoryDirectory($categoryData, $categoryType) {
    $name = $categoryData["name"];
    $original_name = $categoryData["original_name"];

    $baseDirectory = "./images/categories/".($categoryType === "sub" ? format_str($categoryData["parent_category"]) . "/" : "");

    $categoryName = format_str($name);
    $originalCategoryName = format_str($original_name);

    $originalCategoryDirURI = $baseDirectory.$originalCategoryName."/";
    $categoryDirURI = $baseDirectory.$categoryName."/";
    
    if ($name == $original_name) {
        return $originalCategoryDirURI;
    }

    if (renameFolder($originalCategoryDirURI, $categoryDirURI)) {
        return $categoryDirURI;
    }
    else {
        return null;
    }
}

function updateCategoryDirectory($categoryData, $categoryType, $images) {

    $categoryDirURI = renameCategoryDirectory($categoryData, $categoryType);

    if (is_null($categoryDirURI)) {
        return "Sikertelen mappa átnevezés.";
    }
    
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

    return $paths;
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
        $paths = updateCategoryDirectory($categoryData, $categoryType, $images);
    
        if (!is_array($paths)) return $paths;
        if (hasError($paths)) return false;
    
        for ($i = 0; $i < count($images); $i++) {
            $categoryData[$images[$i]["name"]] = $paths[$i];
        }
    }
    else {
        $categoryDirURI = renameCategoryDirectory($categoryData, $categoryType);

        if (is_null($categoryDirURI)) {
            return "Sikertelen mappa átnevezés.";
        }
        
        $table = ($categoryType == "main" ? "category" : "subcategory");
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
        if (!is_array($result)) {
            return "Sikertelen lekérdezés.";
        }

        $currentUris = array_values($result);
        
        for ($i = 0; $i < count($currentUris); $i++) {
            $original_name = format_str($categoryData["original_name"]);
            $name = format_str($categoryData["name"]);
            
            $temp = str_replace($original_name, '|', $currentUris[$i]);
            if ($currentUris[$i] != ""){
                $currentUris[$i] = str_replace('|', $name, $temp);
            }
            else {
                $currentUris[$i] = NULL;
            }
        }

        $query = "UPDATE $table SET $table.thumbnail_image_vertical_uri=?, 
                                    $table.thumbnail_image_horizontal_uri=?,
                                    $table.thumbnail_video_uri=? WHERE $table.id=?";

        if ($table == "category") {
            $values = [...array_slice($currentUris, 0, 3), $categoryData["id"]];
            updateData($query, $values);

            $result = updateData("UPDATE subcategory SET subcategory.thumbnail_image_vertical_uri=?, 
                        subcategory.thumbnail_image_horizontal_uri=?, subcategory.thumbnail_video_uri=? 
                        WHERE subcategory.category_id=?", [...array_slice($currentUris, 3), $categoryData["id"]]);
        }
        else {
            $values = [...$currentUris, $categoryData["id"]];
            updateData($query, $values);
        }
    }

    return updateCategoryData($categoryData, $categoryType, $images);
}