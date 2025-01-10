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

function updateCategoryDirectory($categoryData, $images)
{

    $categoryDirURI = getCategoryDir($categoryData);
    $paths = array();

    foreach ($images as $image) {
        $name = $image["name"];
        $tmp = $image["tmp_name"];
        $ext = $image["ext"];

        $files = scandir($categoryDirURI);

        $existingImage = null;
        foreach ($files as $file) {
            $path = $categoryDirURI . $file;
            if (pathinfo($path, PATHINFO_FILENAME) == $name) {
                $existingImage = $path;
            }
        }

        if ($existingImage) {
            array_push($paths, replaceFile($existingImage, $tmp, "$name.$ext", $name));
        } else {
            array_push($paths, moveFile($tmp, "$name.$ext", $name, $categoryDirURI));
        }
    }

    return new Result(Result::SUCCESS, $paths);
}

function updateCategoryData($categoryData, $images)
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

    foreach ($images as $image) {
        array_push($fields, $image["name"] . "_uri");
        array_push($values, str_replace($_SERVER["DOCUMENT_ROOT"], ROOT_URL, $categoryData[$image["name"]]));
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
    for ($i = 0; $i < count($values) - 1; $i++) {
        $query .= "`{$fields[$i]}`=?";
        if ($i != count($values) - 2)
            $query .= ", ";
    }
    $query .= " WHERE `$table`.`id`=?;";

    return updateData($query, $values, $typeString);
}

function updateCategory($categoryData)
{
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

        if ($result->isError()) {
            return $result;
        }

        $paths = $result->message;
        if (hasError($paths)) {
            return new Result(Result::ERROR, "A képek mozgatásakor hiba merült fel.");
        }

        for ($i = 0; $i < count($images); $i++) {
            $categoryData[$images[$i]["name"]] = $paths[$i];
        }
    }

    $result = updateCategoryData($categoryData, $images);
    if ($result->isError()) {
        return $result;
    }

    $result = updateProductPage($categoryData, $categoryData["type"]);
    if (!$result->isSuccess()) {
        return $result;
    }

    if (count($images) > 0) {
        foreach ($paths as $path) {
            optimizeImage($path);
        }
    }

    return new Result(Result::SUCCESS, "Sikeres módosítás!");
}
