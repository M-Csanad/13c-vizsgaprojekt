<?php

function createCategory($categoryData) {
    include_once 'init.php';
    $db = createConnection();
    
    $categoryType = $categoryData['type'];
    unset($categoryData['type']);
    $thumbnailURI = createCategoryDirectory($categoryData, $categoryType);
    
    if (!$thumbnailURI) {
        return false;
    }
    
    $categoryData['thumbnail_uri'] = $thumbnailURI;
    
    $successful = uploadCategoryData($categoryData, $categoryType);
    return $successful;
}

function createCategoryDirectory($categoryData, $categoryType) {
    if (count(array_filter($_FILES, function ($e) { return $e['error'] == 1; })) > 0) {
        echo "<div class='error'>Hiba merült fel a feltöltés során.</div>";
        return false;
    }

    $baseDirectory = './images/categories/';

    if ($categoryType == 'sub') {
        $baseDirectory .= str_replace(" ", "-", strtolower($categoryData['parent_category']))."/";
    }

    $categoryName = str_replace(" ", "-", strtolower($categoryData['name']));
    $categoryDirURI = $baseDirectory.$categoryName."/";
    
    if (!is_dir($categoryDirURI)) {
        mkdir($categoryDirURI, 0755, true);
    }
    else {
        echo "<div class='error'>Ilyen nevű kategória már létezik.</div>";
        return false;
    }

    $thumbnailTmp = $_FILES['thumbnail_image']['tmp_name'];
    $thumbnail = $_FILES['thumbnail_image']['name'];
    $extension = pathinfo($thumbnail, PATHINFO_EXTENSION);

    $filePath = $categoryDirURI."thumbnail.".$extension;
    $successfulOperation = move_uploaded_file($thumbnailTmp, $filePath);
    if (!$successfulOperation) {
        return false;
    }

    return $filePath;
}

function uploadCategoryData($categoryData, $categoryType) {
    if ($categoryType == "main") {
        $successfulUpload = updateData("INSERT INTO `category`(`name`, `description`, `thumbnail_image_uri`) 
                                        VALUES (?, ?, ?);", array_values($categoryData));
        return $successfulUpload;
    }
    else {
        unset($categoryData['parent_category']);
        $successfulUpload = updateData("INSERT INTO `subcategory`(`name`, `description`, `category_id`, `thumbnail_image_uri`) 
                                        VALUES (?, ?, ?, ?);", array_values($categoryData));
        return $successfulUpload;
    }
}

function removeCategory($categoryData) {
    include_once 'init.php';


    $successfulDelete = removeCategoryFromDB($categoryData);

    if ($successfulDelete === false) {
        return "Ez a kategória nem létezik!";
    }
    else if ($successfulDelete !== true) {
        return $successfulDelete;
    }

    $successfulDirectoryDelete = removeCategoryDirectory($categoryData['name']);
    
    return $successfulDirectoryDelete;
}

function removeCategoryFromDB($categoryData) {
    $db = createConnection();
    
    $query = "DELETE FROM x WHERE x.id = ?;";
    $query = str_replace("x", $categoryData['type'], $query);
    $successfulDelete = updateData($query, $categoryData['id']);

    return $successfulDelete;
}

function removeCategoryDirectory($categoryName) {
    $baseDir = "./images/categories/";
    $categoryName = str_replace(" ", "-", strtolower($categoryName));
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