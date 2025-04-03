<?php
/* --------------------------- Termék létrehozása --------------------------- */

function getProductDir($productData)
{
    $baseDir = $_SERVER["DOCUMENT_ROOT"] . "/fb-content/fb-products/media/images/";
    $dirName = "product-" . $productData["id"] . "/";
    return $baseDir . $dirName;
}

function createProductDirectory($productData)
{
    $paths = [];
    $productDirURI = getProductDir($productData);

    if (!createDirectory($productDirURI)) {
        return new Result(Result::ERROR, "Ilyen nevű termék már létezik a fájlrendszerben. (URL: ".$productDirURI.")");
    }

    $images = ['thumbnail_image'];
    if (isset($_FILES['product_video'])) {
        $images[] = 'product_video';
    }

    foreach ($images as $image) {
        if (isset($_FILES[$image])) {
            $path = saveFile($_FILES[$image], $productDirURI, "thumbnail");
            if (!$path)
                return new Result(Result::ERROR, "Sikertelen kép mentés.");
            $paths[] = $path;
        }
    }

    if (!empty($_FILES['product_images']['name'])) {
        foreach ($_FILES['product_images']['name'] as $index => $name) {
            $path = saveFile(
                [
                    'tmp_name' => $_FILES['product_images']['tmp_name'][$index],
                    'name' => $name
                ],
                $productDirURI,
                "image{$index}"
            );
            if (!$path)
                return new Result(Result::ERROR, "Sikertelen kép mentés.");
            $paths[] = $path;
        }
    }

    return new Result(Result::SUCCESS, $paths);
}

function saveFile($file, $directory, $filename)
{
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $path = $directory . $filename . "." . $extension;
    return move_uploaded_file($file['tmp_name'], $path) ? $path : false;
}


// A termékadatok feltöltése a `product` táblába
function uploadProductData($data)
{

    $fields = array("name", "unit_price", "stock", "description", "net_weight");
    $values = array(
        $data['name'],
        $data['unit_price'],
        $data['stock'],
        $data['description'],
        $data['net_weight']
    );

    $fieldList = implode(", ", $fields);
    $placeholderList = implode(", ", array_fill(0, count($fields), "?"));
    $query = "INSERT INTO `product`($fieldList) VALUES ($placeholderList);";

    return updateData($query, $values, "siisi");
}

// A termék képeinek feltöltése az `image` táblába
function uploadProductImages($paths)
{

    // A feltöltött sorok azonosítóit elmentjük
    $insertIds = array();

    foreach ($paths as $path) {
        $mediaType = getMediaType($path);
        if (str_contains($mediaType, "video")) {
            $orientation = "horizontal";
        } else {
            $orientation = getOrientation($path);
        }

        $result = updateData("INSERT INTO `image`(uri, orientation, media_type) VALUES (?, ?, ?);", [str_replace($_SERVER["DOCUMENT_ROOT"], ROOT_URL, $path), $orientation, $mediaType], "sss");
        if (!$result->isSuccess()) {
            return $result;
        }

        array_push($insertIds, $result->lastInsertId);
    }

    return new Result(Result::SUCCESS, $insertIds);
}

// A A termékképek és a termék összekapcsolása `product_image` táblába való feltöltéssel
function connectProductImages($insertIds, $productId)
{
    foreach ($insertIds as $image) {
        $result = updateData("INSERT INTO `product_image`(image_id, product_id) VALUES (?, ?);", [$image, $productId], "ii");

        if (!$result->isSuccess()) {
            return $result;
        }
    }
    return new Result(Result::SUCCESS, "A termékképek feltöltése sikeres volt.");
}

function connectProductTags($id, $tags)
{
    $placeholderList = implode(", ", array_fill(0, count($tags), "(?, ?)"));
    $values = array();
    $typeString = "";

    foreach ($tags as $tag) {
        array_push($values, intval($tag), intval($id));
        $typeString .= "ii";
    }

    return updateData("INSERT INTO product_tag (tag_id, product_id) VALUES $placeholderList;", $values, $typeString);
}

function connectProductHealthEffects($id, $productHealthEffectsData)
{
    $results = array();

    foreach ($productHealthEffectsData as $type => $effects) {
        $placeholderList = implode(", ", array_fill(0, count($effects), "(?, ?)"));
        $values = array();
        $typeString = "";

        foreach ($effects as $effect) {
            array_push($values, intval($effect), intval($id));
            $typeString .= "ii";
        }

        $result = updateData("INSERT INTO product_health_effect (health_effect_id, product_id) VALUES $placeholderList;", $values, $typeString);
        if (!$result->isSuccess()) {
            return $result;
        }

        array_push($results, $result);
    }

    return new Result(Result::SUCCESS, "Az egészségügyi hatások feltöltése sikeres volt.");
}

// Termék létrehozása - Fő függvény
function createProduct($productData, $productPageData, $productCategoryData, $productHealthEffectsData)
{
    include_once "init.php";

    // Ellenőrizzük, hogy merült-e fel hiba valamelyik fájl feltöltésekor
    if (hasUploadError()) {
        return new Result(Result::ERROR, "Hiba merült fel a feltöltés során.");
    }

    // Adatfeltölés a product táblába
    $result = uploadProductData($productData);
    if ($result->isSuccess()) {
        $productData["id"] = $result->lastInsertId;
        $productPageData["product_id"] = $result->lastInsertId;
    } else if (isError($result)) {
        if ($result->code === 1062) {
            return new Result(Result::ERROR, "Ilyen termék már létezik az adatbázisban.");
        } else {
            return $result;
        }
    } else {
        return new Result(Result::ERROR, "Sikertelen feltöltés az adatbázisba: {$result->toJSON()}");
    }

    // Termékképek mentése a fájlrendszerbe
    $result = createProductDirectory($productData);
    if (!$result->isSuccess()) {
        return new Result(Result::ERROR, "Sikertelen képfeltöltés: {$result->toJSON()}");
    }
    $paths = $result->message;

    // URL-ek feltöltése az adatbázisba
    $result = uploadProductImages($paths);
    if (!$result->isSuccess()) {
        return new Result(Result::ERROR, "Sikertelen feltöltés az image táblába: {$result->toJSON()}");
    }
    $insertIds = $result->message;

    $result = connectProductImages($insertIds, $productData['id']);
    if (!$result->isSuccess()) {
        return new Result(Result::ERROR, "Sikertelen feltöltés a product_image táblába: {$result->toJSON()}");
    }

    // Címkék feltöltése - product_tag
    if (isset($productData['tags'])) {
        $result = connectProductTags($productData['id'], $productData['tags']);
        if (!$result->isSuccess()) {
            return new Result(Result::ERROR, "Sikertelen feltöltés a product_tag táblába: {$result->toJSON()}");
        }
    }

    // Egészségügyi hatások feltöltése - product_health_effects
    if (!empty($productHealthEffectsData)) {
        $result = connectProductHealthEffects($productData['id'], $productHealthEffectsData);
        if (!$result->isSuccess()) {
            return new Result(Result::ERROR, "Sikertelen feltöltés a product_health_effect táblába: {$result->toJSON()}");
        }
    }

    // Alap termékoldal létrehozása
    $result = createProductPage($productData, $productPageData, $productCategoryData);
    if (!$result->isSuccess()) {
        return $result;
    }

    // Feltöltött képek optimalizálása
    foreach ($paths as $path) {
        optimizeImage($path);
    }

    return new Result(Result::SUCCESS, "Sikeres termék létrehozás!");
}


/* ----------------------------- Termék törlése ----------------------------- */

// Termék törlése az adatbázisból
function removeProductFromDB($productData)
{
    $result = updateData("DELETE `image` FROM `image` INNER JOIN product_image ON image.id=product_image.image_id WHERE product_image.product_id=?;", $productData['id'], "i");
    if ($result->isError())
        return $result;

    $result = updateData(
        "DELETE FROM product_page WHERE product_page.product_id = ?;",
        $productData['id'],
        "i"
    ); //Ez egy biztonság miatt van itt, hogy a trigger 100% beinduljon és soha ne legyen hiba.
    if ($result->isError()) {
        return $result;
    }

    return updateData("DELETE FROM product WHERE product.id = ?;", $productData['id'], "i");
}

// Termék mappájának törlése
function removeProductDirectory($productData)
{
    return deleteFolder(getProductDir($productData));
}

// Termék törlése - Fő függvény
function removeProduct($productData)
{
    include_once 'init.php';

    // A kategória törlése az adatbázisból
    $result = removeProductFromDB($productData);
    if ($result->isOfType(Result::NO_AFFECT))
        return new Result(Result::ERROR, "Ez a termék nem létezik!");
    else if ($result->isError())
        return $result;

    // A kategória mappájának törlése
    $successfulDirectoryDelete = removeProductDirectory($productData);
    if (!$successfulDirectoryDelete)
        return new Result(Result::ERROR, "A mappa törlése sikertelen! (A mappát manuálisan kell törölni).");

    return new Result(Result::SUCCESS, "A termék sikeresen törölve lett.");
}

/* ---------------------------- Termék módosítása --------------------------- */

function updateProductHealthEffect($id, $productHealthEffectsData)
{

    $query = "DELETE FROM product_health_effect WHERE product_health_effect.product_id=?;";
    $result = updateData($query, $id, "i");
    if ($result->isError()) {
        return new Result(Result::ERROR, "Sikertelen törlés a product_health_effect táblából.");
    }

    if (!isset($productHealthEffectsData["benefits"]) && !isset($productHealthEffectsData["side_effects"]) || empty($productHealthEffectsData["benefits"]) && empty($productHealthEffectsData["side_effects"]))
        return new Result(Result::SUCCESS, "Nincs változás.");

    $values = array();
    $placeholders = array();
    $typeString = "";

    foreach ($productHealthEffectsData as $type => $effects) {
        foreach ($effects as $effectId) {
            array_push($placeholders, "(?, ?)");
            array_push($values, intval($id), intval($effectId));
            $typeString .= "ii";
        }
    }

    $query = "INSERT INTO product_health_effect (product_id, health_effect_id) VALUES " . implode(", ", $placeholders);
    $result = updateData($query, $values, $typeString);
    if ($result->isError()) {
        return $result;
    }

    return new Result(Result::SUCCESS, "Sikeres törlés.");
}

function updateProductTags($productData)
{

    $query = "DELETE FROM product_tag WHERE product_tag.product_id=?;";
    $result = updateData($query, $productData["id"], "i");
    if ($result->isError()) {
        return new Result(Result::ERROR, "Sikertelen törlés.");
    }

    if (!isset($productData["tags"]))
        return new Result(Result::SUCCESS, "Nincs változás.");

    $values = array();
    $placeholders = array();
    $typeString = "";

    foreach ($productData["tags"] as $tag) {
        array_push($placeholders, "(?, ?)");
        array_push($values, $productData["id"], intval($tag));
        $typeString .= "ii";
    }

    $query = "INSERT INTO product_tag (product_id, tag_id) VALUES " . implode(", ", $placeholders);
    $result = updateData($query, $values, $typeString);
    if ($result->isError()) {
        return new Result(Result::ERROR, "Sikertelen felvitel.");
    }

    return new Result(Result::SUCCESS, "Sikeres törlés.");
}

function updateProductPage($data, $table = null)
{
    if (!$table)
        return new Result(Result::ERROR, "Hiányzó táblázat paraméter.");

    if (selectData("SELECT product_page.id FROM product_page WHERE product_page.{$table}_id=?;", $data["id"], "i")->isEmpty()) {
        return new Result(Result::SUCCESS, "Nincsenek ez alá a kategória alá tartozó termékoldalak.");
    }

    $name = format_str($data["name"]);
    $originalName = format_str($data["original_name"]);

    if ($name == $originalName) {
        return new Result(Result::SUCCESS, "A termékoldal nem változott.");
    }

    $result = selectData("SELECT product_page.id, product_page.link_slug FROM product_page WHERE product_page.{$table}_id=?", $data["id"], "i");
    if (!$result->isSuccess()) {
        return $result;
    }

    $pages = $result->message;

    $slugs = array_map(function ($e) {
        return $e["link_slug"];
    }, $pages);
    $ids = array_map(function ($e) {
        return $e["id"];
    }, $pages);
    $typeString = "";

    for ($i = 0; $i < count($pages); $i++) {
        $slug = $slugs[$i];
        $slugs[$i] = str_replace('|', $name, str_replace($originalName, '|', $slug));
    }

    $caseStatements = [];
    foreach ($ids as $id) {
        $caseStatements[] = "WHEN id = {$id} THEN ?";
        $typeString .= "s";
    }

    $caseSql = implode(' ', $caseStatements);
    $values = implode(', ', $ids);

    $query = "UPDATE product_page SET link_slug = CASE {$caseSql} END, page_title=? WHERE id IN (" . $values . ");";
    $result = updateData($query, [...$slugs, $data["name"]], $typeString . "s");

    if ($result->isError()) {
        return $result;
    }

    return new Result(Result::SUCCESS, "Sikeres módosítás.");
}

function updateProductImages($productData, $imageUpdates)
{
    $insertIds = [];

    prettyPrintArray($productData);
    foreach ($imageUpdates as $imageType => $actions) {
        foreach ($actions as $action => $updates) {
            foreach ($updates as $update) {
                switch ($action) {
                    case 'delete':
                        echo "Kép törlése az adatbázisból";
                        $imageId = intval($update["id"]);
                        $result = updateData("DELETE FROM `image` WHERE id = ?;", $imageId, "i");
                        if ($result->isError()) {
                            return $result;
                        }
                        break;
                    
                    case 'edit':
                        // Módosításnál nem változnak az elérési útvonalak, ezért itt nem kell csinálnunk semmit.
                        break;

                    case 'add':
                        echo "Kép hozzáadása";

                        $path = $update["path"];
                        $path = absoluteToRelativeURL($path);
                        $result = updateData("INSERT INTO image(uri, orientation, media_type) VALUES (?, ?, ?);", [$path, getOrientation($path), "image"], "sss");
                        if ($result->isError() || !$result->lastInsertId) {
                            return $result;
                        }

                        $insertIds[] = $result->lastInsertId;
                        break;
    
                    default:
                        return new Result(Result::ERROR, "Ismeretlen művelet: $action");
                        break;
                }
            }
        }
    }

    if (count($insertIds) > 0) {
        return connectProductImages($insertIds, $productData["id"]);
    }

    return new Result(Result::SUCCESS, "Sikeres képmódosítás!");
}

function updateProductData($productData, $imageUpdates, $productHealthEffectsData)
{

    $fields = array("name", "unit_price", "stock", "description", "net_weight");
    $values = array(
        $productData["name"],
        $productData["price"],
        $productData["stock"],
        $productData["description"],
        $productData["net_weight"],
        $productData["id"]
    );

    $query = "UPDATE `product` SET ";
    for ($i = 0; $i < count($values) - 1; $i++) {
        $query .= "`{$fields[$i]}`=?";
        if ($i != count($values) - 2)
            $query .= ", ";
    }
    $query .= " WHERE `product`.`id`=?;";

    $result = updateData($query, $values, "siisii");
    if ($result->isError()) {
        return new Result(Result::ERROR, "Sikertelen frissítés: ". $result->toJSON());
    }

    $result = updateProductPage($productData, "product");
    if (!$result->isSuccess()) {
        return $result;
    }

    $result = updateProductTags($productData);
    if (!$result->isSuccess()) {
        return $result;
    }

    $result = updateProductHealthEffect($productData['id'], $productHealthEffectsData);
    if (!$result->isSuccess()) {
        return $result;
    }

    if ($imageUpdates) {
        return updateProductImages($productData, $imageUpdates);
    }

    return $result;
}

function updateProductDirectory($productData, &$imageUpdates)
{

    $productDirURI = getProductDir($productData);

    if (!is_dir($productDirURI)) {
        return new Result(Result::ERROR, "Hiányzó mappa: $productDirURI");
    }

    $paths = array();

    echo "<br>Image Updates:<br>";
    prettyPrintArray($imageUpdates);


    foreach ($imageUpdates as $imageType => &$actions) {
        foreach ($actions as $action => &$updates) {
            foreach ($updates as &$update) {
                $fileName = null;
                if ($imageType == "thumbnail_image") {
                    $fileName = "thumbnail";
                } 
                else {
                    $fileName = "image" . $update["index"];
                }
                
                switch ($action) {
                    case 'delete':
                        removeFilesLike($productDirURI, $fileName);
                        break;
                    
                    case 'edit':
                    case 'add':
                        $file = $_FILES[$update["fileKey"]];
                        removeFilesLike($productDirURI, $fileName);
                        $path = moveFile($file["tmp_name"], $file["name"], $fileName, $productDirURI);

                        // Hozzáadjuk az optimalizálandó képekhez
                        $paths[] = $path;

                        // Hozzáadjuk az update objektumhoz
                        if ($action == 'add') {
                            $update["path"] = $path;
                        }
                        break;
    
                    default:
                        return new Result(Result::ERROR, "Ismeretlen művelet: $action");
                        break;
                }
            }
        }
    }

    if (hasError($paths)) {
        return new Result(Result::ERROR, "Hiba a fájlok mozgatása során.");
    }

    return new Result(Result::SUCCESS, $paths);
}

function updateProduct($productData, $productHealthEffectsData, $imageUpdates)
{
    include_once "init.php";

    if (hasUploadError()) {
        return new Result(Result::ERROR, "Hiba merült fel a feltöltés során.");
    }

    $paths = array();

    if ($imageUpdates) {
        $imageUpdates = groupUpdates($imageUpdates);
        $result = updateProductDirectory($productData, $imageUpdates);
        if ($result->isError()) {
            return $result;
        }
        $paths = $result->message;

        if (hasError($paths))
            return new Result(Result::ERROR, "A képek mozgatása sikertelen volt.");

        foreach ($paths as $path) {
            optimizeImage($path);
        }
    }
    var_dump($paths);
    prettyPrintArray($imageUpdates);

    $result = updateProductData($productData, $imageUpdates, $productHealthEffectsData);
    if (!$result->isSuccess()) {
        return $result;
    }

    return new Result(Result::SUCCESS, "Sikeres termék módosítás!");
}
