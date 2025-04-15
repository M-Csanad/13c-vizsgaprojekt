<?php

function getCategoryDir($categoryData)
{
    $baseDir = $_SERVER["DOCUMENT_ROOT"] . "/fb-content/fb-categories/media/images/";

    if ($categoryData["type"] == "subcategory") {
        $baseDir = str_replace("categories", "subcategories", $baseDir);
    }

    $dirName = "category-" . $categoryData["id"] . "/";
    return $baseDir . $dirName;
}

function createCategory($categoryData)
{
    include_once "init.php";

    if (hasUploadError()) {
        return new Result(Result::ERROR, "Hiba merült fel a feltöltés során.");
    }

    $images = array();
    if (isset($_FILES["thumbnail_image_vertical"]))
        array_push($images, "thumbnail_image_vertical");
    if (isset($_FILES["thumbnail_image_horizontal"]))
        array_push($images, "thumbnail_image_horizontal");
    if (isset($_FILES["thumbnail_video"]))
        array_push($images, "thumbnail_video");

    $result = uploadCategoryData($categoryData);
    if (!$result->isSuccess()) {
        return $result;
    }

    $categoryData["id"] = $result->lastInsertId;

    $result = createCategoryDirectory($categoryData, $images);

    if ($result->isError()) {
        return $result;
    }

    $paths = $result->message;
    if (hasError($paths))
        return new Result(Result::ERROR, "Hiba a fájlok mozgatásakor.");

    foreach ($images as $i => $image) {
        $categoryData[$image] = $paths[$i];
    }

    $result = uploadCategoryImages($categoryData);
    if ($result->isError()) {
        return $result;
    }

    foreach ($images as $i => $image) {
        optimizeImage($paths[$i]);
    }

    return new Result(Result::SUCCESS, "Kategória sikeresen létrehozva!");
}


function createCategoryDirectory($categoryData, $images)
{
    $categoryDirURI = getCategoryDir($categoryData);

    if (!createDirectory($categoryDirURI)) {
        return new Result(Result::ERROR, "A mappa létrehozása sikertelen. URI: ".$categoryDirURI);
    }

    $paths = array();

    foreach ($images as $name) {
        $newPath = moveFile($_FILES[$name]["tmp_name"], $_FILES[$name]["name"], $name, $categoryDirURI);

        if ($newPath !== false) {
            array_push($paths, $newPath);
        } else if ($name == "thumbnail_video") {
            array_push($paths, null);
        } else {
            return new Result(Result::ERROR, "Hiba a fájl mozgatásakor ($name).");
        }
    }

    return new Result(Result::SUCCESS, $paths);
}


function uploadCategoryData($categoryData)
{
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
        array_push($values, intval($categoryData["parent_category_id"]));
        $types .= "i";
    }
    $slug = format_str($categoryData["name"]);

    array_push($fields, "slug");
    array_push($values, $slug);
    $types .= "s";

    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `$table`($fieldList) VALUES ($placeholderList);";

    return updateData($query, $values, $types);
}

function uploadCategoryImages($categoryData)
{
    $table = $categoryData["type"];
    $types = array('thumbnail_image_vertical', 'thumbnail_image_horizontal', 'thumbnail_video');

    $fields = array();
    $values = array();
    $typeString = "";

    foreach ($types as $type) {
        if (isset($categoryData[$type])) {
            array_push($values, str_replace($_SERVER["DOCUMENT_ROOT"], ROOT_URL, $categoryData[$type]));
            array_push($fields, $type . "_uri");
            $typeString .= "s";
        }
    }

    $query = "UPDATE $table SET ";

    foreach ($fields as $i => $field) {
        $query .= $field . "=?";
        if ($i != count($fields) - 1) {
            $query .= ", ";
        }
    }

    $query .= " WHERE id=?;";
    $typeString .= "i";
    return updateData($query, [...$values, $categoryData["id"]], $typeString);
}


function removeCategory($categoryData)
{
    include_once "init.php";

    // A kategória törlése az adatbázisból
    $result = removeCategoryFromDB($categoryData);
    if ($result->isError()) {
        return $result;
    } else if ($result->isOfType(Result::NO_AFFECT)) {
        return new Result(Result::ERROR, "A törlendő kategória nem létezik az adatbázisban!");
    }

    // A kategória mappájának törlése
    $result = removeCategoryDirectory($categoryData);
    if (!$result) {
        return new Result(Result::ERROR, "A mappa törlése sikertelen volt!.");
    } else {
        return new Result(Result::SUCCESS, "A mappa sikeresen törölve.");
    }
}

function removeCategoryFromDB($categoryData)
{
    $query = "DELETE FROM {$categoryData["type"]} WHERE {$categoryData["type"]}.id = ?;";
    return updateData($query, $categoryData["id"], "i");
}

function removeCategoryDirectory($categoryData)
{
    $dir = getCategoryDir($categoryData);
    return deleteFolder($dir);
}

function updateCategoryDirectory($categoryData, $imageUpdates)
{
    $categoryDirURI = getCategoryDir($categoryData);
    $paths = array();

    if (!file_exists($categoryDirURI)) {
        return new Result(Result::ERROR, "A kategória mappája nem létezik: " . $categoryDirURI);
    }

    foreach ($imageUpdates as $update) {
        if ($update["action"] !== "edit") {
            continue;
        }

        $imageType = $update["imageType"];
        $fileKey = $update["fileKey"];
        
        if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]["tmp_name"])) {
            continue;
        }

        removeFilesLike($categoryDirURI, "thumbnail_image_" . $imageType);

        $newFileName = "thumbnail_image_" . $imageType;
        
        $path = moveFile($_FILES[$fileKey]["tmp_name"], $_FILES[$fileKey]["name"], $newFileName, $categoryDirURI);
        if (!$path) {
            return new Result(Result::ERROR, "Hiba a fájl mozgatásakor ($fileKey).");
        }
        
        $paths[] = $path;
    }

    return new Result(Result::SUCCESS, $paths);
}

function updateCategoryData($categoryData)
{
    $table = $categoryData["type"];
    $isMainCategory = $table == "category";
    $slug = format_str($categoryData["name"]);

    $fields = array("name", "subname", "description", "slug");
    $values = array(
        $categoryData["name"],
        $categoryData["subname"],
        $categoryData["description"],
        $slug
    );
    $typeString = "ssss";

    if (!$isMainCategory) {
        array_push($fields, "category_id");
        array_push($values, $categoryData["parent_category_id"]);
        $typeString .= "i";
    }

    array_push($values, $categoryData["id"]);
    $typeString .= "i";

    $query = "UPDATE `$table` SET ";
    for ($i = 0; $i < count($fields); $i++) {
        $query .= "`{$fields[$i]}`=?";
        if ($i != count($fields) - 1)
            $query .= ", ";
    }
    $query .= " WHERE `$table`.`id`=?;";

    return updateData($query, $values, $typeString);
}

function updateCategory($categoryData, $imageUpdates)
{
    include_once "init.php";

    if (hasUploadError()) {
        return new Result(Result::ERROR, "Hiba merült fel a feltöltés során.");
    }

    if ($imageUpdates) {
        $result = updateCategoryDirectory($categoryData, $imageUpdates);
        
        if ($result->isError()) {
            return $result;
        }

        $paths = $result->message;
        if (hasError($paths)) {
            return new Result(Result::ERROR, "A képek mozgatásakor hiba merült fel.");
        }

        foreach ($paths as $path) {
            optimizeImage($path);
        }
    }
    
    $result = updateCategoryData($categoryData);
    if ($result->isError()) {
        return $result;
    }
    
    return new Result(Result::SUCCESS, "Sikeres módosítás!");
}


/**
 * Visszaadja a kategóriák teljes számát az adatbázisból.
 *
 * @return QueryResult A lekérdezés eredménye, amely tartalmazza a termékek teljes számát.
 */
function getTotalCategories()
{
    return selectData("SELECT COUNT(*) as total FROM category");
}


/**
 * Lekérdezi az összes alkategória számát.
 *
 * @return QueryResult A lekérdezés eredménye, amely tartalmazza az adatbázisban található összes alkategória darabszámát.
 */
function getTotalSubcategories()
{
    return selectData("SELECT COUNT(*) as total FROM subcategory");
}