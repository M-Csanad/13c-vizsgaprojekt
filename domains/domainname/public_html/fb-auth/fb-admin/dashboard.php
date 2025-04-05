<?php 
    include_once $_SERVER["DOCUMENT_ROOT"].'/config.php';
    include_once "../../../../../.ext/init.php"; 

    $isLoggedIn = false;
    $result = getUserData();
    if ($result->isSuccess()) {
      $user = $result->message[0];
      $isLoggedIn = true;

      if ($user["role"] !== "Administrator") {
          header("Location: ./");
          exit();
      }
    }
    else {
        header("Location: ./");
        exit();
    }


    $result = selectData("SELECT * FROM health_effect");
    if ($result->isError()) {
        $healthEffectError = "Nem sikerült betölteni az egészségügyi hatásokat.";
    }
    else {
        if (!is_array($result->message)) {
            $healthEffectError = "Nincsenek megadva egészségügyi hatások.";
        }
        else {
            $healthEffects = $result->message;

            $benefits = array_filter($healthEffects, function ($e) {return $e["benefit"] == 1;});
            $sideEffects = array_filter($healthEffects, function ($e) {return $e["benefit"] == 0;});
        }
    }

    $result = selectData("SELECT * FROM tag;");
    if ($result->isSuccess()) {
        $tags = $result->message;
    }
    else if ($result->isEmpty()){
        $tags = "Nincsenek allergének felvéve.";
    }


    // Űrlap beküldés kezelése
    // Kategória létrehozása
    if (isset($_POST['create_category'])) {

        $categoryData = array(
            "name" => $_POST['category_name'],
            "subname" => $_POST['category_subname'],
            "type" => ($_POST['type'] == "sub") ? "subcategory" : "category",
            "description" => $_POST['description']);

        if (isset($_POST['parent_category'])) {
            $categoryData['parent_category'] = $_POST['parent_category'];
            $categoryData['parent_category_id'] = intval($_POST['parent_category_id']);
        }

        $result = createCategory($categoryData);

        if ($result->isSuccess()) {
            $message = "<div class='success'>Kategória sikeresen létrehozva!</div></div>";
        }
        else {
            $message = "<div class='error'>A kategória létrehozása sikertelen! {$result->message}</div></div>";
        }

    }

    // Kategória törlése
    if (isset($_POST['delete_category'])) {

        if ($_POST['category_type'] == 'null' || $_POST['category_id'] == 'null') {
            $message = "<div class='error'>A kategória törlése sikertelen! Kérjük töltsön ki minden mezőt!</div></div>"; 
        }
        else {
            $categoryData = array(
                "name" => $_POST['category_name'],
                "type" => $_POST['category_type'],
                "id" => intval($_POST['category_id'])
            );
            $result = removeCategory($categoryData);

            if ($result->isSuccess()) {
                $message = "<div class='success'>A kategória sikeresen törölve.</div></div>";
            }
            else {
                $message = "<div class='error'>A kategória törlése sikertelen! {$result->message}</div></div>";
            }
        }
    }

    // Kategória módosítása
    if (isset($_POST['modify_category'])) {

        $categoryData = array(
            "id" => intval($_POST['category_id']),
            "name" => $_POST['name'],
            "original_name" => $_POST['category_name'],
            "type" => isset($_POST['parent_category']) ? "subcategory" : "category",
            "subname" => $_POST['subname'],
            "description" => $_POST['description']);

        if (isset($_POST['parent_category'])) {
            $categoryData['parent_category'] = $_POST['parent_category'];
            $categoryData['original_parent_category'] = $_POST['original_parent_category'];
            $categoryData['parent_category_id'] = intval($_POST['parent_category_id']);
        }

        $imageUpdates = $_POST['image_updates'] ?? null;
        if ($imageUpdates) {
            $imageUpdates = json_decode($imageUpdates, true);
        }

        $result = updateCategory($categoryData, $imageUpdates);
        
        if (!$result->isError()) {
            $message = "<div class='success'>A kategória sikeresen módosítva.</div></div>";
        }
        else {
            $message = "<div class='error'>A kategória módosítása sikertelen! {$result->message}</div></div>";
        }
    }

    // Termék létrehozása
    if (isset($_POST['create_product'])) {
        $productData = array(
            "name" => $_POST['product_name'],
            "unit_price" => intval($_POST['price']),
            "stock" => intval($_POST['stock']),
            "description" => $_POST['description'],
            "net_weight" => intval($_POST['net_weight'])
        );
        if (isset($_POST["tags"])) $productData["tags"] = $_POST['tags'];

        $productPageData = array(
            "product_id" => null, // Termékfeltöltés után lesz beállítva
            "link_slug" => null, // Létrehozáskor meghatározzuk a kategória és alkategória neveiből
            "category_id" => intval($_POST['category_id']),
            "subcategory_id" => intval($_POST['subcategory_id']),
            "page_title" => $_POST['product_name'],
            "page_content" => $_POST['content']
        );

        $productCategoryData = array(
            "category" => $_POST['category'],
            "subcategory" => $_POST['subcategory']
        );

        $productHealthEffectsData = array();
        if ($_POST["benefits"] != "null") {
            $productHealthEffectsData["benefits"] = explode(",", $_POST["benefits"]);
        }
        if ($_POST["side_effects"] != "null") {
            $productHealthEffectsData["side_effects"] = explode(",", $_POST["side_effects"]);
        }

        $result = createProduct($productData, $productPageData, $productCategoryData, $productHealthEffectsData);

        if (!$result->isError()) {
            $message = "<div class='success'>Termék sikeresen létrehozva!</div></div>";
        }
        else {
            $message = "<div class='error'>A termék létrehozása sikertelen! {$result->message}</div></div>";
        }
    }

    // Termék módosítása
    if (isset($_POST['modify_product'])) {
        $productData = array(
            "id" => intval($_POST['product_id']),
            "original_name" => $_POST['product_name'],
            "name" => $_POST['name'],
            "description" => $_POST['description'],
            "price" => intval($_POST['price']),
            "stock" => intval($_POST['stock']),
            "net_weight" => intval($_POST['net_weight'])
        );

        if (isset($_POST["tags"])) $productData["tags"] = $_POST['tags'];

        $productHealthEffectsData = array();
        if ($_POST["benefits"] != "null") {
            $productHealthEffectsData["benefits"] = explode(",", $_POST["benefits"]);
        }
        if ($_POST["side_effects"] != "null") {
            $productHealthEffectsData["side_effects"] = explode(",", $_POST["side_effects"]);
        }

        $imageUpdates = $_POST['image_updates'] ?? null;
        if ($imageUpdates) {
            $imageUpdates = json_decode($imageUpdates, true);
        }
         
        $result = updateProduct($productData, $productHealthEffectsData, $imageUpdates);
        if (!$result->isError()) {
            $message = "<div class='success'>Termék sikeresen módosítva!</div></div>";
        }
        else {
            $message = "<div class='error'>A termék módosítása sikertelen! {$result->message}</div></div>";
        }
    }
    
    //Termék törlése
    if (isset($_POST['delete_product'])) {
        $productData = array(
            "id" => intval($_POST['product_id']),
            "name" => $_POST['product_name']
        );

        $result = removeProduct($productData);

        if (!$result->isError()) {
            $message = "<div class='success'>A termék sikeresen törölve.</div>";
        }
        else {
            $message = "<div class='error'>A termék törlése sikertelen! {$result->message}</div>";
        }
    }

    // Termék oldal létrehozása
    if (isset($_POST['create_product_page'])) {
        
        $productData = array(
            "name" => $_POST["product_name"],
            "id" => intval($_POST["product_id"])
        );

        $productPageData = array(
            "product_id" => null, // Termékfeltöltés után lesz beállítva
            "link_slug" => null, // Létrehozáskor meghatározzuk a kategória és alkategória neveiből
            "category_id" => intval($_POST['category_id']),
            "subcategory_id" => intval($_POST['subcategory_id']),
            "page_title" => $_POST['product_name'],
            "page_content" => $_POST['content']
        );

        $productCategoryData = array(
            "category" => $_POST['category'],
            "subcategory" => $_POST['subcategory'],
        );

        $result = createProductPage($productData, $productPageData, $productCategoryData);
        if (!$result->isError()) {
            $message = "<div class='success'>Termék oldal sikeresen létrehozva!</div>";
        }
        else {
            $message = "<div class='error'>A termék oldal létrehozása sikertelen! {$result->message}</div>";
        }
    }

    // Termék oldal törlése
    if (isset($_POST['delete_product_page'])) {
        $result = removeProductPage(intval($_POST['product_page_id']));

        if (!isError($result)) {
            $message = "<div class='success'>A termék oldal sikeresen törölve.</div>";
        }
        else {
            $message = "<div class='error'>A termék oldal törlése sikertelen! {$result->message}</div>";
        }
    }

    // Termék oldal módosítása
    if (isset($_POST['modify_product_page'])) {
        $productPageData = array(
            "id" => intval($_POST['product_page_id']),
            "page_title" => $_POST['product_page_name'],
            "page_content" => $_POST['content'],
            "category_id" => intval($_POST['category_id']),
            "subcategory_id" => intval($_POST['subcategory_id'])
        );

        $categoryData = array(
            "category" => $_POST['category'],
            "subcategory" => $_POST['subcategory'],
        );

        $result = modifyProductPage($productPageData, $categoryData);
        if (!$result->isError()) {
            $message = "<div class='success'>A termék oldal módosítva.</div>";
        }
        else {
            $message = "<div class='error'>A termék oldal módosítása sikertelen! {$result->toJSON()}</div>";
        }
    }
    
    // Jogosultság változtatása
    if (isset($_POST['modify_role'])) {
        $userId = intval($_POST['user_id']);
        $role = $_POST['role'];
        if (modifyRole($userId, $role)->isSuccess()) {
            $message = "<div class='success'>Sikeres művelet!</div>";
        }
        else {
            $message = "<div class='error'>A művelet sikertelen!</div>";
        }
    }

    // Rendelés állapotának módosítása
    if (isset($_POST['update_order_status'])) {
        $orderId = intval($_POST['order_id']);
        $newStatus = $_POST['order_status'];

        $result = updateOrderStatus($orderId, $newStatus);
        
        if (!$result->isError()) {
            $message = "<div class='success'>A rendelés állapota sikeresen frissítve.</div>";
        }
        else {
            $message = "<div class='error'>A rendelés állapotának módosítása sikertelen! {$result->message}</div>";
        }
    }

    if (isset($_POST['delete_user'])) {
        $userId = intval($_POST['user_id']);
        $result = deleteUser($userId);
        
        if (!$result->isError()) {
            $message = "<div class='success'>A felhasználó sikeresen törölve.</div>";
        }
        else {
            $message = "<div class='error'>A felhasználó törlése sikertelen! {$result->message}</div>";
        }
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A Florens Botanica vezérlőpultja">
    <title>Vezérlőpult</title>

    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="preload" href="./fb-auth/assets/fonts/Raleway.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="./fb-auth/assets/css/root.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/dashboard.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/allergen-checkbox.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/search.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/table.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/loader.css">
    <link rel="stylesheet" href="./fb-auth/assets/css/multiselect.css">

    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script type="module" defer src="./fb-auth/assets/js/dashboard.js"></script>
    <script defer src="./fb-auth/assets/js/search.js"></script>
    <script defer src="./fb-auth/assets/js/tag-checkbox.js"></script>
    <script defer src="./fb-auth/assets/js/loader.js"></script>
    <script defer src="./fb-auth/assets/js/multiselect.js"></script>
    <script src="./fb-auth/assets/js/prevent-resubmit.js"></script>
</head>
<body>
    <p>
        <a href="./" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" fill="currentColor"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
            </svg>
            Vissza a főoldalra
        </a>
    </p>
    <header class="page-title">Vezérlőpult</header>

    <nav class="pages">
        <div class="page active" data-pageid="0" tabindex="0">
            Kategóriák
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="1" tabindex="0">
            Termékek
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="2" tabindex="0">
            Oldalak
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="3" tabindex="0">
            Jogosultságok
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
        <div class="page" data-pageid="4" tabindex="0">
            Felhasználók
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
            </svg>
        </div>
    </nav>

    <!------------------------------ Kategóriák kezelése ----------------------------->
    <div class="section-group active">
        <div class="group-body">
            <!-------------------------- Új kategória létrehozása ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Új kategória létrehozása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="" data-show-loader="true">
                        <div class="input-grid">
                            <div class="form-divider">Általános adatok</div>
                            <div class="inline-input">
                                <label for="category_name"><div>Név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="category_name" id="category_name" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="category_subname"><div>Alcím</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="category_subname" id="category_subname" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="type"><div>Típus</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="type" id="type" required>
                                            <option value="main">Főkategória</option>
                                            <option value="sub">Alkategória</option>
                                        </select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="parent_category"><div>Főkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="parent_category" id="parent_category" disabled required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="parent_category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="file-input inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class='label-tooltip'>
                                        Függőleges tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_vertical" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="thumbnail_image_vertical" id="thumbnail_image_vertical" required accept="image/jpeg" data-orientation="vertical" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_horizontal" class="input-wrapper" tabindex="0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="thumbnail_image_horizontal" id="thumbnail_image_horizontal" required accept="image/jpeg"  data-orientation="horizontal" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_category'>
                        </div>
                    </form>
                </div>
            </section>

            <!-------------------------- Kategória törlése ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Kategória törlése</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" data-needs-confirm="true" data-confirm-message="A kategória törlése nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="category" data-id-input="category_id" data-type-input="category_type">
                                    <input type="text" name="category_name" id="category_name_delete" placeholder="Kategória keresése" required autocomplete="off">
                                    <label for="category_name_delete" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="category_id" id="category_id_delete" value="null">
                                    <input type="hidden" name="category_type" id="category_type_delete" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Törlés" class="form-submit-danger" name='delete_category'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>

            <!-------------------------- Kategória módosítása ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Kategória módosítása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-role="modify" data-title="category-modify" data-needs-confirm="true" data-confirm-message="A kategória módosítása nem visszavonható művelet!" data-show-loader="true">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-autofill-fields="true" data-search-type="category" data-id-input="category_id_modify" data-type-input="category_type_modify">
                                    <input type="text" name="category_name" id="category_name_modify_search" placeholder="Kategória keresése" required autocomplete="off">
                                    <label for="category_name_modify_search" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="category_id" id="category_id_modify" value="null">
                                    <input type="hidden" name="category_type" id="category_type_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Általános adatok</div>
                            <div class="inline-input">
                                <label for="category_name_modify"><div>Név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="name" id="category_name_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="category_subname_modify"><div>Alcím</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="subname" id="category_subname_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description_modify"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="parent_category_modify"><div>Főkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="parent_category" id="parent_category_modify" disabled required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="parent_category_id" value="null">
                                        <input type="hidden" name="original_parent_category" value="null">
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class='label-tooltip'>
                                        Függőleges tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container image-cards vertical no-scrollbar">
                                        <div class="none-selected">
                                            Nincs kiválasztva kategória!
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </div>
                                    <div class='label-tooltip'>
                                        Vízszintes tájolású kép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container image-cards horizontal no-scrollbar">
                                        <div class="none-selected">
                                            Nincs kiválasztva kategória!
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-submit-wrapper">
                                <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_category'>
                            </div>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Termékek kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <!----------------------------- Új termék felvitele ---------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Új termék felvitele</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="" data-show-loader="true">
                        <div class="input-grid">
                            <div class="form-divider">Általános termékadatok</div>
                            <div class="inline-input">
                                <label for="product_name"><div>Termék név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="product_name" id="product_name" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="price"><div>Egységár</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="price" id="price" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="stock"><div>Készlet</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="stock" id="stock" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="net_weight"><div><span>Kiszerelés <i>(g)</i></span></div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="net_weight" id="net_weight" required>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input tag-wrapper">
                                <label><div>Allergének</div></label>
                                <div class="tag-body">
                                    <div class="tag-items">
                                        <?php if (is_string($tags)): ?>
                                            <?= htmlspecialchars($tags) ?>
                                        <?php else: ?>
                                            <?php foreach ($tags as $index => $tag): ?>
                                                <label for='tag<?= htmlspecialchars($index); ?>' class='tag-checkbox'><img loading='lazy' src='<?= htmlspecialchars($tag['icon_uri']) ?>' draggable='false' title='<?= htmlspecialchars($tag['name']) ?>' alt='<?= htmlspecialchars($tag['name']) ?>'><input type='checkbox' name='tags[]' id='tag<?= htmlspecialchars($index) ?>' value='<?= htmlspecialchars($tag['id']) ?>'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check2 tag-check' viewBox='0 0 16 16'><path d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0'/></svg></label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="benefits-select" class="fill-width">Jótékony hatások</label>
                                <div class="input-content no-overflow">
                                    <div class="input-container">
                                        <?php if (isset($healthEffectError) && !empty($healthEffectError)): ?>
                                            <div style="color: #771201"><?= htmlspecialchars($healthEffectError) ?></div>
                                        <?php else: ?>
                                            <div class="multiselect" id="benefits-select">
                                                <div class="body">
                                                    <div class="selected-item-count">Elemek kiválasztása</div>
                                                    <div class="expander">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <div class="multiselect-items" name="benefits-container">
                                                        <div class="search-input">
                                                            <input type="text" class="multiselect-filter" placeholder="Keresés...">
                                                        </div>
                                                        <div class="option visible" data-label-value="Select All">
                                                            <div class="check">
                                                                <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                            </div>
                                                            <div class="label">Összes kiválasztása</div>
                                                        </div>
                                                        <hr />
                                                        <?php foreach ($benefits as $index=>$benefit): ?>
                                                            <div class="option visible" data-value="<?= htmlspecialchars($benefit["id"])?>" data-label-value="<?= htmlspecialchars($benefit["name"])?>">
                                                                <div class="check">
                                                                    <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                                </div>
                                                                <div class="label"><?= htmlspecialchars($benefit["name"]) ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <div class="no-result">Nincsenek találatok!</div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="benefits" class="multiselect-selectedItems" value="null" />
                                            </div>
                                        <?php endif; ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="side-effects-select">Mellékhatások</label>
                                <div class="input-content no-overflow">
                                    <div class="input-container">
                                        <?php if (isset($healthEffectError) && !empty($healthEffectError)): ?>
                                            <div style="color: #771201"><?= htmlspecialchars($healthEffectError) ?></div>
                                        <?php else: ?>
                                            <div class="multiselect" id="side-effects-select">
                                                <div class="body">
                                                    <div class="selected-item-count">Elemek kiválasztása</div>
                                                    <div class="expander">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <div class="multiselect-items" name="side-effects-container">
                                                        <div class="search-input">
                                                            <input type="text" class="multiselect-filter" placeholder="Keresés...">
                                                        </div>
                                                        <div class="option visible" data-label-value="Select All">
                                                            <div class="check">
                                                                <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                            </div>
                                                            <div class="label">Összes kiválasztása</div>
                                                        </div>
                                                        <hr />
                                                        <?php foreach ($sideEffects as $index=>$sideEffect): ?>
                                                            <div class="option visible" data-value="<?= htmlspecialchars($sideEffect["id"]) ?>" data-label-value="<?= htmlspecialchars($sideEffect["name"])?>">
                                                                <div class="check">
                                                                    <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                                </div>
                                                                <div class="label"><?= htmlspecialchars($sideEffect["name"]) ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <div class="no-result">Nincsenek találatok!</div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="side_effects" class="multiselect-selectedItems" value="null" />
                                            </div>
                                        <?php endif; ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Termékoldal adatai</div>
                            <div class="inline-input">
                                <label for="category"><div>Kategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="category" id="product_category_create" required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory"><div>Alkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="subcategory" id="product_subcategory_create" required data-table="subcategory"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="subcategory_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content"><div>Tartalom</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <textarea name="content" id="content_create" required></textarea>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="file-input inline-input">
                                <label><div>Borítókép</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="thumbnail_image_create" class="input-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="thumbnail_image" id="thumbnail_image_create" required accept="image/jpeg" data-orientation="any" data-type="image" data-count="singular" tabindex="-1">
                                                Kép kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="file-input inline-input">
                                <label><div>Képek</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <div class="main-wrapper">
                                            <label for="product_images_create" class="input-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                                </svg>
                                                <input type="file" name="product_images[]" id="product_images_create" multiple required accept="image/jpeg" data-type="image" data-count="multiple" data-orientation="any">
                                                Képek kiválasztása
                                            </label>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_product'>
                        </div>
                    </form>
                </div>
            </section>
            <!------------------------------- Termék törlése ------------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Termék törlése</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" data-needs-confirm="true" data-confirm-message="A termék törlése nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product" data-id-input="product_id">
                                    <input type="text" name="product_name" id="product_name_delete" placeholder="Termék keresése" required autocomplete="off">
                                    <label for="product_name_delete" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_id" id="product_id_delete" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-submit-wrapper">
                                <input type="submit" value="Törlés" class="form-submit-danger" name='delete_product'>
                            </div>
                        </div>
                    </form>
                    <div class="items">
                        <!-- <div class="loader-wrapper">
                            <div class="loader dot-spinner">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </section>
            <!------------------------------ Termék módosítása ----------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title"><div>Termék módosítása</div></div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-role="modify" data-title="product-modify" data-needs-confirm="true" data-confirm-message="A termék módosítása nem visszavonható művelet!" data-show-loader="true">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product" data-id-input="product_id" data-autofill-fields="true">
                                    <input type="text" name="product_name" id="product_name_modify" placeholder="Termék keresése" required autocomplete="off">
                                    <label for="product_name_modify" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_id" id="product_id_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Általános termékadatok</div>
                            <div class="inline-input">
                                <label for="name_modify"><div>Termék név</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="name" id="name_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="description_modify"><div>Leírás</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="text" name="description" id="description_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="price_modify"><div>Egységár</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="price" id="price_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="stock_modify"><div>Készlet</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="stock" id="stock_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="net_weight_modify"><div><span>Kiszerelés <i>(g)</i></span></div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <input type="number" name="net_weight" id="net_weight_modify" required disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input tag-wrapper">
                                <label><div>Allergének</div></label>
                                <div class="tag-body">
                                    <div class="tag-items" name="tags">
                                        <?php if (is_string($tags)): ?>
                                            <?= htmlspecialchars($tags) ?>
                                        <?php else: ?>
                                            <?php foreach ($tags as $index => $tag): ?>
                                                <label for='tag<?= htmlspecialchars($index); ?>-modify' class='tag-checkbox'><img loading='lazy' src='<?= htmlspecialchars($tag['icon_uri']) ?>' draggable='false' title='<?= htmlspecialchars($tag['name']) ?>' alt='<?= htmlspecialchars($tag['name']) ?>'><input type='checkbox' name='tags[]' id='tag<?= htmlspecialchars($index) ?>-modify' value='<?= htmlspecialchars($tag['id']) ?>'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-check2 tag-check' viewBox='0 0 16 16'><path d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0'/></svg></label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="benefits-select-modify" class="fill-width">Jótékony hatások</label>
                                <div class="input-content no-overflow">
                                    <div class="input-container">
                                        <?php if (isset($healthEffectError) && !empty($healthEffectError)): ?>
                                            <div style="color: #771201"><?= htmlspecialchars($healthEffectError) ?></div>
                                        <?php else: ?>
                                            <div class="multiselect" id="benefits-select-modify">
                                                <div class="body">
                                                    <div class="selected-item-count">Elemek kiválasztása</div>
                                                    <div class="expander">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <div class="multiselect-items" name="benefits-container">
                                                        <div class="search-input">
                                                            <input type="text" class="multiselect-filter" placeholder="Keresés...">
                                                        </div>
                                                        <div class="option visible" data-label-value="Select All">
                                                            <div class="check">
                                                                <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                            </div>
                                                            <div class="label">Összes kiválasztása</div>
                                                        </div>
                                                        <hr />
                                                        <?php foreach ($benefits as $index=>$benefit): ?>
                                                            <div class="option visible" data-value="<?= htmlspecialchars($benefit["id"])?>" data-label-value="<?= htmlspecialchars($benefit["name"])?>">
                                                                <div class="check">
                                                                    <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                                </div>
                                                                <div class="label"><?= htmlspecialchars($benefit["name"]) ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <div class="no-result">Nincsenek találatok!</div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="benefits" class="multiselect-selectedItems" value="null" />
                                            </div>
                                        <?php endif; ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="side-effects-select-modify">Mellékhatások</label>
                                <div class="input-content no-overflow">
                                    <div class="input-container">
                                        <?php if (isset($healthEffectError) && !empty($healthEffectError)): ?>
                                            <div style="color: #771201"><?= htmlspecialchars($healthEffectError) ?></div>
                                        <?php else: ?>
                                            <div class="multiselect" id="side-effects-select-modify">
                                                <div class="body">
                                                    <div class="selected-item-count">Elemek kiválasztása</div>
                                                    <div class="expander">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="dropdown">
                                                    <div class="multiselect-items" name="side-effects-container">
                                                        <div class="search-input">
                                                            <input type="text" class="multiselect-filter" placeholder="Keresés...">
                                                        </div>
                                                        <div class="option visible" data-label-value="Select All">
                                                            <div class="check">
                                                                <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                            </div>
                                                            <div class="label">Összes kiválasztása</div>
                                                        </div>
                                                        <hr />
                                                        <?php foreach ($sideEffects as $index=>$sideEffect): ?>
                                                            <div class="option visible" data-value="<?= htmlspecialchars($sideEffect["id"]) ?>" data-label-value="<?= htmlspecialchars($sideEffect["name"])?>">
                                                                <div class="check">
                                                                    <img src="./fb-auth/assets/svg/check.svg" alt="" draggable="false" />
                                                                </div>
                                                                <div class="label"><?= htmlspecialchars($sideEffect["name"]) ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <div class="no-result">Nincsenek találatok!</div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="side_effects" class="multiselect-selectedItems" value="null" />
                                            </div>
                                        <?php endif; ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="form-divider">Média elemek</div>
                            <div class="inline-input">
                                <label>
                                    <div>
                                        Borítókép
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container image-cards thumbnail no-scrollbar">
                                        <div class="none-selected">
                                            Nincs kiválasztva termék!
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label>
                                    <div>
                                        Képek
                                    </div>
                                </label>
                                <div class="input-content">
                                    <div class="input-container image-cards product-images no-scrollbar">
                                        <div class="none-selected">
                                            Nincs kiválasztva termék!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_product'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Termékek oldalak kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <!----------------------------- Új termék oldal felvitele ---------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Új termék oldal felvitele</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product" data-id-input="product_id">
                                    <input type="text" name="product_name" id="product_name_page_create" placeholder="Termék keresése" required autocomplete="off">
                                    <label for="product_name_page_create" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_id" id="product_id_page_create" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Termék oldal adatai</div>
                            <div class="inline-input">
                                <label for="category"><div>Kategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="category" id="product_category_page_create" required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory"><div>Alkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="subcategory" id="product_subcategory" required data-table="subcategory"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="subcategory_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content"><div>Tartalom</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <textarea name="content" id="content" required></textarea>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_product_page'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>

            <!---------------------------- Termék oldal törlése ---------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Termék oldal törlése</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="true" data-confirm-message="A termék oldal törlése nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product_page">
                                    <input type="text" name="product_page_name" id="product_page_name_delete" placeholder="Termék oldal keresése" required autocomplete="off">
                                    <label for="product_name_page_delete" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_page_id" id="product_page_id_delete" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Törlés" class="form-submit-danger" name='delete_product_page'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>

            <!--------------------------- Termék oldal módosítása -------------------------->
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Termék oldal módosítása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data" data-needs-confirm="false" data-confirm-message="A termék oldal módosítása nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="product_page" data-id-input="product_page_id" data-autofill-fields="true">
                                    <input type="text" name="product_page_name" id="product_page_name_modify" placeholder="Termék oldal keresése" required autocomplete="off">
                                    <label for="product_name_page_delete" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="product_page_id" id="product_page_id_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="form-divider">Termék oldal adatai</div>
                            <div class="inline-input">
                                <label for="category"><div>Kategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select disabled name="category" id="product_category_page_modify" required data-table="category"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="category_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory"><div>Alkategória</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select disabled name="subcategory" id="product_subcategory_page_modify" required data-table="subcategory"></select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                        <input type="hidden" name="subcategory_id" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content"><div>Tartalom</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <textarea disabled name="content" id="content_modify" required></textarea>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_product_page'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Jogosultságok kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Jogosultság módosítása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" id="form-role" data-needs-confirm="true" data-confirm-message="Adminisztrátori jogokkal csak megbízható személyeket lásson el!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="user" data-id-input="user_id">
                                    <input type="text" name="user_name" id="user_name_modify" placeholder="Felhasználó keresése" required autocomplete="off">
                                    <label for="user_name_modify" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="user_id" id="user_id_modify" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            <div class="inline-input">
                                <label for="role"><div>Jogosultság</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="role" id="role" required disabled>
                                            <option value="Guest">Guest</option>
                                            <option value="Administrator">Administrator</option>
                                            <option value="Bot">Bot</option>
                                        </select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_role'>
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <!------------------------------ Felhasználók kezelése ----------------------------->
    <div class="section-group">
        <div class="group-body">
            <!-------------------------- Rendelés módosítása ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Rendelés állapotának módosítása</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" data-needs-confirm="false">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="order" data-id-input="order_id" data-autofill-fields="true">
                                    <input type="number" name="order_search" id="order_search" placeholder="Rendelés azonosítója" required autocomplete="off">
                                    <label for="order_search" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="order_id" id="order_id" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                            
                            <div class="inline-input">
                                <label for="order_status"><div>Állapot</div></label>
                                <div class="input-content">
                                    <div class="input-container">
                                        <select name="order_status" id="order_status" required disabled>
                                            <option value="Visszaigazolva">Visszaigazolva</option>
                                            <option value="Feldolgozás alatt">Feldolgozás alatt</option>
                                            <option value="Szállítás alatt">Szállítás alatt</option>
                                            <option value="Teljesítve">Teljesítve</option>
                                        </select>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                        </svg>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-ban disabled" viewBox="0 0 16 16">
                                            <path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Állapot frissítése" class="form-submit-primary" name="update_order_status">
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>

            <!-------------------------- Felhasználó törlése ------------------------>
            <section>
                <div class="section-header" tabindex="0">
                    <div class="section-title">Felhasználó törlése</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down section-expander" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div class="section-body">
                    <form method="POST" data-needs-confirm="true" data-confirm-message="A felhasználó törlése nem visszavonható művelet!">
                        <div class="input-grid">
                            <div class="search-wrapper">
                                <div class="search" data-search-type="user" data-id-input="user_id" data-autofill-fields="true">
                                    <input type="text" name="user_search" id="user_search" placeholder="Felhasználó azonosítója / neve" required autocomplete="off">
                                    <label for="user_search" class="search-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                        </svg>
                                    </label>
                                    <input type="hidden" name="user_id" id="user_id" value="null">
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2 valid" viewBox="0 0 16 16">
                                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg invalid" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Törlés" class="form-submit-danger" name="delete_user">
                        </div>
                    </form>
                    <div class="items"></div>
                </div>
            </section>
        </div>
    </div>
    <?php if (isset($message) && !empty($message)) echo $message; ?>
</body>
</html>