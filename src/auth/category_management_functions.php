<?php

function format_str($s) {
    return str_replace(" ", "-", strtolower($s));
}

function hasUploadError() {
    return count(array_filter($_FILES, function ($e) { return $e['error'] == 1; })) > 0;
}

function hasError($paths) {
    return count(array_filter($paths, function ($e) {return $e === false; })) > 0;
}

function createCategory($categoryData) {
    include_once 'init.php';
    $db = createConnection();
    
    $categoryType = $categoryData['type'];
    unset($categoryData['type']);

    $images = array('thumbnail_image_vertical', 'thumbnail_image_horizontal', 'thumbnail_video');
    $paths = createCategoryDirectory($categoryData, $categoryType, $images);

    if (!is_array($paths)) return $paths;
    if (hasError($paths)) return false;

    for ($i = 0; $i < count($images); $i++) {
        $categoryData[$images[$i]] = $paths[$i];
    }
    
    return uploadCategoryData($categoryData, $categoryType);
}

function createCategoryDirectory($categoryData, $categoryType, $images) {
    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return "Hiba merült fel a feltöltés során.";
    }

    $baseDirectory = './images/categories/'.($categoryType === 'sub' ? format_str($categoryData['parent_category']) . "/" : "");
    $categoryDirURI = $baseDirectory . format_str($categoryData['name'])."/";
    
    if (!createDirectory($categoryDirURI)) {
        return "Ilyen nevű kategória már létezik.";
    }


    $paths = array();

    foreach ($images as $name) {
        $newPath = moveFile($_FILES[$name]['tmp_name'], $_FILES[$name]['name'], $name, $categoryDirURI);

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

function createDirectory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        return true;
    }

    return false;
}

function moveFile($tmp, $name, $basename,$dir) {
    $extension = pathinfo($name, PATHINFO_EXTENSION);
    
    $filePath = $dir."$basename.".$extension;
    $successfulOperation = move_uploaded_file($tmp, $filePath);
    if (!$successfulOperation) {
        return false;
    }
    
    return $filePath;
}

function uploadCategoryData($categoryData, $categoryType) {
    $isMainCategory = ($categoryType == "main");
    $table = $isMainCategory ? "category" : "subcategory";
    
    $fields = array("name", "subname", "description", "thumbnail_image_horizontal_uri", "thumbnail_image_vertical_uri");
    $values = array(
        $categoryData['name'],
        $categoryData['subname'],
        $categoryData['description'],
        $categoryData['thumbnail_image_horizontal'],
        $categoryData['thumbnail_image_vertical']
    );
    
    if (!empty($categoryData['thumbnail_video'])) {
        array_push($fields, "thumbnail_video_uri");
        array_push($values, $categoryData['thumbnail_video']);
    }
    
    if (!$isMainCategory) {
        array_push($fields, "category_id");
        array_push($values, $categoryData['category_id']);
    }
    
    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `$table`($fieldList) VALUES ($placeholderList);";
    
    return updateData($query, $values);
}


function removeCategory($categoryData) {
    include_once 'init.php';

    // Az alkategóriához tartozó főkategória lekérdezése
    if ($categoryData['type'] == "subcategory") {
        $result = selectData("SELECT category.name 
                                FROM subcategory 
                                INNER JOIN category 
                                ON subcategory.category_id = category.id 
                                WHERE subcategory.id = ?", $categoryData['id']);

        // Ha nincs főkategóriája, akkor nincs olyan alkategória, mivel kötelező a category_id
        if ($result == "Nincs találat!") {
            return "Ez a kategória nem létezik!";
        }

        $categoryData['parent_category'] = format_str($result['name']);
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
    $db = createConnection();
    
    $query = "DELETE FROM x WHERE x.id = ?;";
    $query = str_replace("x", $categoryData['type'], $query);
    $successfulDelete = updateData($query, $categoryData['id']);

    return $successfulDelete;
}

function removeCategoryDirectory($categoryData) {
    $baseDir = "./images/categories/";
    $categoryName = str_replace(" ", "-", strtolower($categoryData['name']));

    if ($categoryData['type'] == "subcategory") {
        $baseDir .= $categoryData['parent_category']."/";
    }

    $categoryDirURI = $baseDir.$categoryName."/";

    $successfulDelete = deleteFolder($categoryDirURI);

    return $successfulDelete;
}

function deleteFolder($folderPath) {
    if (!is_dir($folderPath)) {
        return false;
    }
    $files = scandir($folderPath);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

        if (is_dir($filePath)) {
            deleteFolder($filePath);
        } else {
            unlink($filePath);
        }
    }

    return rmdir($folderPath);
}
?>