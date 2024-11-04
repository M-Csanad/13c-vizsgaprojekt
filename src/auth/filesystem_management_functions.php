<?php

function format_str($s) {
    return str_replace(" ", "-", strtolower($s));
}

function getOrientation($image) {
    $size = getimagesize($image);
    if ($imageInfo) {
        $width = $size[0];
        $height = $size[1];

        return ($width >= $height) ? "horizontal" : "vertical";

    } else {
        return "Failed to get image dimensions.";
    }
}

function createDirectory($paths) {
    if (!is_array($paths)) $paths = [$paths];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        else {
            return false;
        }
    }
    
    return true;
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

function moveFile($tmp, $name, $basename, $dir) {
    $extension = pathinfo($name, PATHINFO_EXTENSION);
    
    $filePath = $dir."$basename.".$extension;
    $successfulOperation = move_uploaded_file($tmp, $filePath);
    if (!$successfulOperation) {
        return false;
    }
    
    return $filePath;
}

function hasError($paths) {
    return count(array_filter($paths, function ($e) {return $e === false; })) > 0;
}

function hasUploadError() {
    return count(array_filter($_FILES, function ($e) { return $e['error'] == 1; })) > 0;
}

?>