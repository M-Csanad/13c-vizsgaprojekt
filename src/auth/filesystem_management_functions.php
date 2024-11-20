<?php

function format_str($s) {
    $hungarian_to_english = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 
        'ó' => 'o', 'ö' => 'o', 'ő' => 'o',
        'ú' => 'u', 'ü' => 'u', 'ű' => 'u'
    ];

    $temp = str_replace(" ", "-", trim(mb_strtolower($s)));
    return strtr($temp, $hungarian_to_english);
}

function getOrientation($image) {
    $size = getimagesize($image);
    if ($size) {
        $width = $size[0];
        $height = $size[1];

        return ($width >= $height) ? "horizontal" : "vertical";

    } else {
        return "Nem sikerült a kép tájolásának kinyerése.";
    }
}

function getMediaType($image) {
    return mime_content_type($image);
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

function moveFolder($folderPath, $newFolderPath) {

    if (!is_dir($folderPath)) {
        return false;
    }

    if (!is_dir($newFolderPath)) {
        if (!mkdir($newFolderPath, 0777, true)) {
            return false;
        }
    }

    $files = scandir($folderPath);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $sourcePath = $folderPath . DIRECTORY_SEPARATOR . $file;
        $destinationPath = $newFolderPath . DIRECTORY_SEPARATOR . $file;

        if (is_file($sourcePath)) {
            if (!rename($sourcePath, $destinationPath)) {
                return false;
            }
        }
    }

    return deleteFolder($folderPath);
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

function renameFolder($folderPath, $newFolderPath) {
    if (!is_dir($folderPath)) {
        return false;
    }

    return rename($folderPath, $newFolderPath);
}

function deleteFolderFiles($folderPath) {
    if (!is_dir($folderPath)) {
        return false;
    }

    $files = scandir($folderPath);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

        if (is_file($filePath)) {
            unlink($filePath);
        } elseif (is_dir($filePath)) {
            continue;
        }
    }

    return true;
}

function moveFile($tmp, $name, $basename, $dir) {
    $extension = pathinfo($name, PATHINFO_EXTENSION);
    
    $filePath = $dir."$basename.".$extension;
    if (!move_uploaded_file($tmp, $filePath)) {
        return false;
    }
    
    return $filePath;
}

function replaceFile($fileURI, $tmp, $name, $basename) {
    $dirURI = pathinfo($fileURI, PATHINFO_DIRNAME)."/";

    if (is_file($fileURI)) {
        unlink($fileURI);
        if (moveFile($tmp, $name, $basename, $dirURI)) return $fileURI;
        else return false;
    }
    else return false;
}

function hasError($paths) {
    return count(array_filter($paths, function ($e) {return $e === false; })) > 0;
}

function hasUploadError() {
    return count(array_filter($_FILES, function ($e) { return $e['error'] == 1; })) > 0;
}

?>