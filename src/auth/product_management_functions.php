<?php

function createProduct() { //$_FILES és $_POST látszódik, mivel szuperglobális, így nem kell paraméterként kezelni.
    include_once "init.php";
    $baseDirectory = './images/products/';

    if (count(array_filter($_FILES, function ($e) { return $e['error'] == 1; })) > 0) {
        echo "<div class='error'>Hiba merült fel a feltöltés során.</div>";
        return false;
    }
    
    $productName = str_replace(" ", "-", strtolower($_POST['product_name']));
    $productDirURI = $baseDirectory.$productName."/";
    
    if (!is_dir($productDirURI)) {
        mkdir($productDirURI, 0755, true);
        mkdir($productDirURI.'thumbnail/', 0755, true);
        mkdir($productDirURI.'gallery/', 0755, true);
    }
    else {
        echo "<div class='error'>Ilyen nevű termék már létezik.</div>";
        return false;
    }

    $thumbnailTmp = $_FILES['thumbnail_image']['tmp_name'];
    $thumbnail = $_FILES['thumbnail_image']['name'];
    $extension = pathinfo($thumbnail, PATHINFO_EXTENSION);

    $successfulUpload = move_uploaded_file($thumbnailTmp, $productDirURI."thumbnail/thumbnail".$extension);
    if (!$successfulUpload) {
        return false;
    }

    $fileCount = count($_FILES['product_images']['name']);
    for ($i=0; $i < $fileCount; $i++) {
        $productImageTmp = $_FILES['product_images']['tmp_name'][$i];
        $productImage = $_FILES['product_images']['name'][$i];
        $extension = pathinfo($productImage, PATHINFO_EXTENSION);

        $successfulUpload = move_uploaded_file($productImageTmp, $productDirURI."gallery/image".$i.$extension);
        if (!$successfulUpload) {
            return false;
        }
    }

    return true;
}

?>