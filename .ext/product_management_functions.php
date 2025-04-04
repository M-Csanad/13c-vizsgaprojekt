<?php
/* --------------------------- Termék létrehozása --------------------------- */

/**
 * Visszaadja a termék könyvtárának elérési útját a megadott termékadatok alapján.
 *
 * @param array $productData A termék adatai, amelyek alapján a könyvtár elérési útja meghatározásra kerül.
 * @return string A termék könyvtárának elérési útja.
 */
function getProductDir($productData)
{
    $baseDir = $_SERVER["DOCUMENT_ROOT"] . "/fb-content/fb-products/media/images/";
    $dirName = "product-" . $productData["id"] . "/";
    return $baseDir . $dirName;
}


/**
 * Létrehozza a termékhez tartozó könyvtárat és elmenti a feltöltött fájlokat.
 *
 * @param array $productData A termék adatai, amely alapján a könyvtár létrejön.
 * 
 * @return Result A művelet eredménye:
 *                - SUCCESS: Ha a könyvtár létrejött és a fájlok sikeresen elmentésre kerültek.
 *                - ERROR: Ha a könyvtár már létezik, vagy a fájlok mentése sikertelen.
 *
 * A függvény a következő lépéseket hajtja végre:
 * 1. Meghatározza a termék könyvtárának URI-ját a `getProductDir` függvény segítségével.
 * 2. Létrehozza a könyvtárat a `createDirectory` függvény segítségével.
 *    - Ha a könyvtár már létezik, hibát ad vissza.
 * 3. Elmenti a feltöltött fájlokat:
 *    - `thumbnail_image`: A termék indexképe.
 *    - `product_video`: (opcionális) A termékhez tartozó videó.
 *    - `product_images`: Több kép, amely a termékhez tartozik.
 * 4. Ha bármelyik fájl mentése sikertelen, hibát ad vissza.
 * 5. Ha minden sikeres, visszaadja a mentett fájlok elérési útvonalait.
 *
 * Megjegyzés: A `$_FILES` globális változót használja a feltöltött fájlok eléréséhez.
 */
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

/**
 * Feltölti a termékadatokat a `product` táblába.
 *
 * @param array $data A termék adatai, amelyeket fel kell tölteni.
 * 
 * @return QueryResult A művelet eredménye. Siker esetén a feltöltött sorok azonosítóit tartalmazza.
 * 
 * A függvény a megadott adatokat beszúrja a `product` adatbázis táblába.
 */
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

/**
 * Termék képek feltöltését végző függvény.
 *
 * @param array $paths A feltöltendő képek vagy videók fájlútvonalainak tömbje.
 * 
 * @return Result A művelet eredménye. Siker esetén a feltöltött sorok azonosítóit tartalmazza.
 * 
 * A függvény a megadott fájlok alapján meghatározza a média típusát (kép vagy videó),
 * valamint a tájolást (ha kép). Ezután az adatokat elmenti az `image` adatbázis táblába.
 * Ha bármelyik beszúrás sikertelen, a függvény azonnal visszatér a hibával.
 */
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

/**
 * A megadott képek és a termék közötti kapcsolatot hozza létre az adatbázisban.
 *
 * @param array $insertIds Azoknak a képeknek az azonosítói, amelyeket a termékhez kell kapcsolni.
 * @param int $productId A termék azonosítója, amelyhez a képeket kapcsolni kell.
 * 
 * @return Result A művelet eredményét tartalmazó objektum. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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

/**
 * Összekapcsolja a termékeket a címkékkel az adatbázisban.
 *
 * @param int $id A termék azonosítója, amelyhez a címkéket kapcsolni kell.
 * @param array $tags Egy tömb, amely a kapcsolni kívánt címkék azonosítóit tartalmazza.
 *
 * @return QueryResult A művelet eredményét tartalmazó objektum. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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

/**
 * Összekapcsolja a termékeket az egészségügyi hatásokkal az adatbázisban.
 *
 * @param int $id A termék azonosítója, amelyhez az egészségügyi hatásokat kapcsolni kell.
 * @param array $productHealthEffectsData Egy tömb, amely a kapcsolni kívánt egészségügyi hatásokat tartalmazza.
 *
 * @return Result A művelet eredményét tartalmazó objektum. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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

/**
 * Létrehozza a terméket az adatbázisban és a fájlrendszerben.
 *
 * @param array $productData A termék adatai, amelyeket fel kell tölteni.
 * @param array $productPageData A termékoldal adatai.
 * @param array $productCategoryData A termékkategória adatai.
 * @param array $productHealthEffectsData A termék egészségügyi hatásai.
 *
 * @return Result A művelet eredménye. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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

/**
 * Törli a terméket az adatbázisból.
 *
 * @param array $productData A termék adatai, amelyeket törölni kell.
 *
 * @return Result A művelet eredménye. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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
/**
 * Törli a termék könyvtárát a fájlrendszerből.
 *
 * @param array $productData A termék adatai, amelynek könyvtárát törölni kell.
 *
 * @return bool True, ha a törlés sikeres volt, különben false.
 */
function removeProductDirectory($productData)
{
    return deleteFolder(getProductDir($productData));
}

// Termék törlése - Fő függvény
/**
 * Törli a terméket az adatbázisból és a fájlrendszerből.
 *
 * @param array $productData A termék adatai, amelyeket törölni kell.
 *
 * @return Result A művelet eredménye. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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

/* ---------------------------- Termék módosítása ---------------------------- */

/**
 * Frissíti a termék egészségügyi hatásait az adatbázisban.
 *
 * @param int $id A termék azonosítója.
 * @param array $productHealthEffectsData Az egészségügyi hatások adatai, amelyeket frissíteni kell.
 *        A tömb két kulcsot tartalmazhat: "benefits" és "side_effects", amelyek egészségügyi hatás azonosítókat tartalmaznak.
 *
 * @return Result A művelet eredménye:
 *         - Result::SUCCESS, ha a frissítés sikeres volt vagy nem történt változás.
 *         - Result::ERROR, ha hiba történt a frissítés során.
 *
 * A függvény először törli a megadott termékhez tartozó összes egészségügyi hatást a `product_health_effect` táblából.
 * Ha a megadott adatok üresek, akkor nem történik további művelet, és sikeres eredményt ad vissza.
 * Ha vannak új egészségügyi hatások, azokat beszúrja a táblába.
 */
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

/**
 * Frissíti a termék címkéit az adatbázisban.
 *
 * @param array $productData A termék adatai, amelyeket frissíteni kell.
 *        A tömbnek tartalmaznia kell a "tags" kulcsot, amely egy tömböt tartalmaz a címkék azonosítóival.
 *
 * @return Result A művelet eredménye:
 *         - Result::SUCCESS, ha a frissítés sikeres volt vagy nem történt változás.
 *         - Result::ERROR, ha hiba történt a frissítés során.
 *
 * A függvény először törli a megadott termékhez tartozó összes címkét a `product_tag` táblából.
 * Ha a megadott adatok üresek, akkor nem történik további művelet, és sikeres eredményt ad vissza.
 * Ha vannak új címkék, azokat beszúrja a táblába.
 */
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

/**
 * Frissíti a termékoldalakat az adatbázisban.
 *
 * @param array $data A termékoldal adatai, amelyeket frissíteni kell.
 *        A tömbnek tartalmaznia kell a "name" és "original_name" kulcsokat.
 * @param string|null $table Az adatbázis tábla neve, amelyhez a frissítést végre kell hajtani.
 *
 * @return Result A művelet eredménye:
 *         - Result::SUCCESS, ha a frissítés sikeres volt vagy nem történt változás.
 *         - Result::ERROR, ha hiba történt a frissítés során.
 *
 * A függvény először ellenőrzi, hogy van-e termékoldal a megadott kategória alatt.
 * Ha nincs, akkor sikeres eredményt ad vissza. Ha van, akkor frissíti a link_slug és page_title mezőket.
 */
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

/**
 * Frissíti a termék képeit az adatbázisban.
 *
 * @param array $productData A termék adatai, amelyeket frissíteni kell.
 * @param array $imageUpdates A képek frissítési műveletei (pl. törlés, szerkesztés, hozzáadás).
 *
 * @return Result A művelet eredménye:
 *         - Result::SUCCESS, ha a frissítés sikeres volt.
 *         - Result::ERROR, ha hiba történt a frissítés során.
 *
 * A függvény végrehajtja a megadott képek frissítési műveleteit (törlés, szerkesztés, hozzáadás)
 * és visszaadja a művelet eredményét.
 */
function updateProductImages($productData, $imageUpdates)
{
    $insertIds = [];

    foreach ($imageUpdates as $imageType => $actions) {
        foreach ($actions as $action => $updates) {
            foreach ($updates as $update) {
                switch ($action) {
                    case 'delete':
                        $imageId = intval($update["id"]);
                        $result = updateData("DELETE FROM `image` WHERE id = ?;", $imageId, "i");
                        if ($result->isError()) {
                            return $result;
                        }
                        break;
                    
                    case 'edit':
                        $path = $update["path"];
                        $path = absoluteToRelativeURL($path);
                        $result = updateData("UPDATE `image` SET uri = ?, orientation = ? WHERE id = ?", [$path, getOrientation($path), $update["id"]], "sss");
                        if ($result->isError()) {
                            return $result;
                        }

                        break;

                    case 'add':
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

/**
 * Frissíti a termék adatait az adatbázisban.
 *
 * @param array $productData A termék adatai, amelyeket frissíteni kell.
 * @param array $imageUpdates A képek frissítési műveletei (pl. törlés, szerkesztés, hozzáadás).
 * @param array $productHealthEffectsData A termék egészségügyi hatásai.
 *
 * @return Result A művelet eredménye:
 *         - Result::SUCCESS, ha a frissítés sikeres volt.
 *         - Result::ERROR, ha hiba történt a frissítés során.
 *
 * A függvény végrehajtja a megadott termékadatok frissítési műveleteit és visszaadja a művelet eredményét.
 */
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

/**
 * Frissíti a termék könyvtárát és a fájlokat a fájlrendszerben.
 *
 * @param array $productData A termék adatai, amelyeket frissíteni kell.
 * @param array $imageUpdates A képek frissítési műveletei (pl. törlés, szerkesztés, hozzáadás).
 *
 * @return Result A művelet eredménye:
 *         - Result::SUCCESS, ha a frissítés sikeres volt.
 *         - Result::ERROR, ha hiba történt a frissítés során.
 *
 * A függvény végrehajtja a megadott képek frissítési műveleteit és visszaadja a művelet eredményét.
 */
function updateProductDirectory($productData, &$imageUpdates)
{

    $productDirURI = getProductDir($productData);

    if (!is_dir($productDirURI)) {
        return new Result(Result::ERROR, "Hiányzó mappa: $productDirURI");
    }

    $paths = array();

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
                        $update["path"] = $path;
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

/**
 * Frissíti a terméket az adatbázisban és a fájlrendszerben.
 *
 * @param array $productData A termék adatai, amelyeket frissíteni kell.
 * @param array $productHealthEffectsData A termék egészségügyi hatásai.
 * @param array $imageUpdates A képek frissítési műveletei (pl. törlés, szerkesztés, hozzáadás).
 *
 * @return Result A művelet eredménye. Siker esetén SUCCESS állapotot ad vissza,
 *                hiba esetén a megfelelő hibainformációval tér vissza.
 */
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

    $result = updateProductData($productData, $imageUpdates, $productHealthEffectsData);
    if (!$result->isSuccess()) {
        return $result;
    }
    
    return new Result(Result::SUCCESS, "Sikeres termék módosítás!");
}
