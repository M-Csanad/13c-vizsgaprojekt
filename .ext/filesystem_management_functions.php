<?php

function format_str($s)
{
    $hungarian_to_english = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'Á' => 'a',
        'É' => 'e',
        'Í' => 'i',
        'Ó' => 'o',
        'Ö' => 'o',
        'Ő' => 'o',
        'Ú' => 'u',
        'Ü' => 'u',
        'Ű' => 'u'
    ];

    $s = strtr(mb_strtolower($s), $hungarian_to_english);
    $s = preg_replace('/[^a-z0-9 -]/', '', $s);
    $s = str_replace(' ', '-', $s);
    $s = preg_replace('/-+/', '-', $s);

    return trim($s, '-');
}

function reverse_format_str($s)
{
    return str_replace("-", " ", $s);
}

function getOrientation($image)
{
    $size = getimagesize($image);
    if ($size) {
        $width = $size[0];
        $height = $size[1];

        return ($width >= $height) ? "horizontal" : "vertical";

    } else {
        return "Nem sikerült a kép tájolásának kinyerése.";
    }
}

function getMediaType($image)
{
    return mime_content_type($image);
}

function createDirectory($paths)
{
    if (!is_array($paths))
        $paths = [$paths];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        } else {
            return false;
        }
    }

    return true;
}

function deleteFolder($folderPath)
{
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

function moveFile($tmp, $name, $basename, $dir)
{
    $extension = pathinfo($name, PATHINFO_EXTENSION);

    $filePath = $dir . "$basename." . $extension;
    if (!move_uploaded_file($tmp, $filePath)) {
        return false;
    }

    return $filePath;
}

function replaceFile($fileURI, $tmp, $name, $basename)
{
    $dirURI = pathinfo($fileURI, PATHINFO_DIRNAME) . "/";

    if (is_file($fileURI)) {
        unlink($fileURI);
        $result = moveFile($tmp, $name, $basename, $dirURI);
        return $result;
    } else
        return false;
}

function hasError($paths)
{
    return count(array_filter($paths, function ($e) {
        return $e === false;
    })) > 0;
}

function hasUploadError()
{
    return count(array_filter($_FILES, function ($e) {
        return $e['error'] == 1;
    })) > 0;
}

function removeFilesLike($dirURI, $needle) {
    $files = scandir($dirURI);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        if (strpos($file, $needle) !== false) {
            unlink($dirURI . $file);
        }
    }
}

function groupUpdates($updates)
{
    $desired = ['delete', 'edit', 'add'];
    $grouped = [];

    if (is_array($updates)) {
        $types = [];
        foreach ($updates as $update) {
            if (isset($update['imageType']) && !in_array($update['imageType'], $types)) {
                $types[] = $update['imageType'];
            }
        }
        
        foreach ($types as $type) {
            $grouped[$type] = array_fill_keys($desired, []);
        }
        
        foreach ($updates as $update) {
            if (isset($update['imageType']) && isset($update['action']) && in_array($update['action'], $desired)) {
                $grouped[$update['imageType']][$update['action']][] = $update;
            }
        }
    }

    foreach ($grouped as $type => &$actions) {
        foreach ($actions as $action => $items) {
            if (empty($items)) {
                unset($actions[$action]);
            }
        }
    }
    return $grouped;
}

function prettyPrintArray($array, $level = 0) 
{
    $output = "<pre>\n";
    $output .= formatArrayRecursive($array, $level);
    $output .= "</pre>";
    echo $output;
}

function formatArrayRecursive($array, $level = 0) 
{
    if (!is_array($array)) {
        return formatValue($array);
    }
    
    $indent = str_repeat("    ", $level);
    $output = "Array (" . count($array) . ") {\n";
    
    foreach ($array as $key => $value) {
        $output .= $indent . "    [" . (is_string($key) ? "\"$key\"" : $key) . "] => ";
        
        if (is_array($value)) {
            $output .= "\n" . $indent . "    " . formatArrayRecursive($value, $level + 1);
        } else {
            $output .= formatValue($value) . "\n";
        }
    }
    
    $output .= $indent . "}\n";
    return $output;
}

function formatValue($value) 
{
    if (is_string($value)) {
        return "string(" . strlen($value) . ") \"$value\"";
    } elseif (is_int($value)) {
        return "int($value)";
    } elseif (is_float($value)) {
        return "float($value)";
    } elseif (is_bool($value)) {
        return "bool(" . ($value ? "true" : "false") . ")";
    } elseif (is_null($value)) {
        return "NULL";
    } elseif (is_object($value)) {
        return "object(" . get_class($value) . ")";
    } elseif (is_resource($value)) {
        return "resource(" . get_resource_type($value) . ")";
    } else {
        return gettype($value) . "($value)";
    }
}