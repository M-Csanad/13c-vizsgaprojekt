<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vezérlőpult</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <?php 
        include_once "./auth/init.php"; 

        session_start();
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] != "Administrator") {
                header("Location: ./");
                exit();
            }
        }
        else {
            header("Location: ./");
            exit();
        }
    ?>
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

    <!------------------------------ Jogosultságok kezelése ----------------------------->
    <div class="section-group">
        <div class="group-header">
            <div class="group-title">Jogosultságok kezelése</div>
            <div class="group-expander">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                </svg>
            </div>
        </div>
        <div class="group-body">
            <section></section>
        </div>
    </div>

    <!------------------------------ Kategóriák kezelése ----------------------------->
    <div class="section-group">
        <div class="group-header">
            <div class="group-title">Kategóriák kezelése</div>
            <div class="group-expander">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                </svg>
            </div>
        </div>
        <div class="group-body">
            <!-------------------------- Új kategória létrehozása ------------------------>
            <section>
                <div class="section-title">Új kategória létrehozása</div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data">
                    <div class="input-grid">
                            <div class="inline-input">
                                <label for="category_name">Név</label>
                                <input type="text" name="category_name" id="category_name" required>
                            </div>
                            <div class="inline-input">
                                <label for="description">Leírás</label>
                                <input type="text" name="description" id="description" required>
                            </div>
                            <div class="inline-input">
                                <label for="type">Típus</label>
                                <select name="type" id="type" required>
                                    <option value="main">Főkategória</option>
                                    <option value="sub">Alkategória</option>
                                </select>
                            </div>
                            <div class="inline-input">
                                <label for="parent_category">Főkategória</label>
                                <select name="parent_category" id="parent_category" disabled required>
                                    <?php
                                        $categories = selectData("SELECT * FROM category;", null);
                                        if (is_array($categories)) {
                                            // Ha csak egy főkategória van, akkor átalakítjuk a megfelelő formátumra.
                                            if (!is_array(array_values($categories)[0])) $categories = [$categories]; 

                                            foreach ($categories as $category) {
                                                echo "<option value='{$category['name']}' data-id='{$category['id']}'>{$category['name']}</option>";
                                            }
                                        }
                                    ?>
                                </select>
                                <input type="hidden" name="parent_category_id" disabled value="0">
                            </div>
                            <div class="file-input inline-input">
                                <label>Borítókép</label>
                                <div>
                                    <label for="thumbnail_image" class="input-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload upload" viewBox="0 0 16 16">
                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                        </svg>
                                        <input type="file" name="thumbnail_image" id="thumbnail_image" required accept="image/png, image/jpeg">
                                        Kép kiválasztása
                                    </label>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(57, 152, 65)" class="bi bi-check2-all check" viewBox="0 0 16 16">
                                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Felvitel" class="form-submit-primary" name='create_category'>
                        </div>
                    </form>
                </div>
            </section>
            <section>
                <div class="section-title">Kategória törlése</div>
                <div class="section-body">
                    <form method="POST">
                        <div class="input-grid">
                            <div class="search">
                                <input type="text" name="category_name" id="category_name_delete">
                                <label for="category_name_delete" class="search-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </label>
                            </div>
                            <input type="hidden" name="selected_type" id="selected_type_delete">
                            <div class="form-submit-wrapper">
                                <input type="submit" value="Törlés" class="form-submit-danger" name='delete_product'>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <!------------------------------ Termékek kezelése ----------------------------->
    <div class="section-group">
        <div class="group-header">
            <div class="group-title">Termékek kezelése</div>
            <div class="group-expander">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                </svg>
            </div>
        </div>
        <div class="group-body">
            <!----------------------------- Új termék felvitele ---------------------------->
            <section>
                <div class="section-title">Új termék felvitele</div>
                <div class="section-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="input-grid">
                            <div class="inline-input">
                                <label for="product_name">Termék név</label>
                                <input type="text" name="product_name" id="product_name" required>
                            </div>
                            <div class="inline-input">
                                <label for="description">Leírás</label>
                                <input type="text" name="description" id="description" required>
                            </div>
                            <div class="inline-input">
                                <label for="price">Egységár</label>
                                <input type="number" name="price" id="price" required>
                            </div>
                            <div class="inline-input">
                                <label for="stock">Készlet</label>
                                <input type="number" name="stock" id="stock" required>
                            </div>
                            <div class="inline-input">
                                <label for="category">Kategória</label>
                                <input type="text" name="category" id="category" required>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory">Alkategória</label>
                                <input type="text" name="subcategory" id="subcategory" required>
                            </div>
                            <div class="file-input inline-input">
                            <label>Borítókép</label>
                            <div>
                                <label for="thumbnail_image_create" class="input-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                    </svg>
                                    <input type="file" name="thumbnail_image" id="thumbnail_image_create" required accept="image/png, image/jpeg">
                                    Kép kiválasztása
                                </label>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(57, 152, 65)" class="bi bi-check2-all check" viewBox="0 0 16 16">
                                    <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                    <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                </svg>
                                </div>
                            </div>
                            <div class="file-input inline-input">
                                <label>Termékképek</label>
                                <div>
                                    <label for="product_images_create" class="input-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                        </svg>
                                        <input type="file" name="product_images[]" id="product_images_create" multiple required accept="image/png, image/jpeg">
                                        Képek kiválasztása
                                    </label>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(57, 152, 65)" class="bi bi-check2-all check" viewBox="0 0 16 16">
                                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content">Tartalom</label>
                                <input type="text" name="content" id="content" required>
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
                <div class="section-title">Termék törlése</div>
                <div class="section-body">
                    <form method="POST">
                        <div class="input-select">
                            <label for="termek">Termék kiválasztása</label>
                            <select name="termek" id="termek">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Törlés" class="form-submit-danger" name='delete_product'>
                        </div>
                    </form>
                </div>
            </section>
            <!------------------------------ Termék módosítása ----------------------------->
            <section>
                <div class="section-title">Meglévő termék szerkesztése</div>
                <div class="section-body">
                <form method="POST" enctype="multipart/form-data">
                        <div class="input-grid">
                            <div class="inline-input">
                                <label for="product_name">Termék név</label>
                                <input type="text" name="product_name" id="product_name" required>
                            </div>
                            <div class="inline-input">
                                <label for="description">Leírás</label>
                                <input type="text" name="description" id="description" required>
                            </div>
                            <div class="inline-input">
                                <label for="price">Egységár</label>
                                <input type="number" name="price" id="price" required>
                            </div>
                            <div class="inline-input">
                                <label for="stock">Készlet</label>
                                <input type="number" name="stock" id="stock" required>
                            </div>
                            <div class="inline-input">
                                <label for="category">Kategória</label>
                                <input type="text" name="category" id="category" required>
                            </div>
                            <div class="inline-input">
                                <label for="subcategory">Alkategória</label>
                                <input type="text" name="subcategory" id="subcategory" required>
                            </div>
                            <div class="file-input inline-input">
                            <label>Borítókép</label>
                            <div>
                                <label for="thumbnail_image_change" class="input-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                    </svg>
                                    <input type="file" name="thumbnail_image" id="thumbnail_image_change" required accept="image/png, image/jpeg">
                                    Kép kiválasztása
                                </label>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(57, 152, 65)" class="bi bi-check2-all check" viewBox="0 0 16 16">
                                    <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                    <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                </svg>
                                </div>
                            </div>
                            <div class="file-input inline-input">
                                <label>Termékképek</label>
                                <div>
                                    <label for="product_images_change" class="input-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-upload" viewBox="0 0 16 16">
                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                                        </svg>
                                        <input type="file" name="product_images[]" id="product_images_change" multiple required accept="image/png, image/jpeg">
                                        Képek kiválasztása
                                    </label>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(57, 152, 65)" class="bi bi-check2-all check" viewBox="0 0 16 16">
                                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="inline-input">
                                <label for="content">Tartalom</label>
                                <input type="text" name="content" id="content" required>
                            </div>
                        </div>
                        <div class="form-submit-wrapper">
                            <input type="submit" value="Módosítás" class="form-submit-primary" name='modify_product'>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div>
        <?php
            if (isset($_POST['create_category'])) {
                unset($_POST['create_category']);

                $categoryData = array(
                    "name" => $_POST['category_name'],
                    "type" => $_POST['type'],
                    "description" => $_POST['description']);

                if (isset($_POST['parent_category'])) {
                    $categoryData['parent_category'] = $_POST['parent_category'];
                    $categoryData['parent_category_id'] = $_POST['parent_category_id'];
                }

                $successfulOperation = createCategory($categoryData);

                if ($successfulOperation === true) {
                    echo "Kategória sikeresen létrehozva!";
                }
                else {
                    echo "<div class='error'>A kategória létrehozása sikertelen!</div>";
                }
            }

            if (isset($_POST['create_product'])) {
                unset($_POST['create_product']);
                $successfulOperation = createProduct();

                if ($successfulOperation) {
                    echo "Termék sikeresen létrehozva!";
                }
                else {
                    echo "<div class='error'>A termék létrehozása sikertelen!</div>";
                }
            }

        ?>
    </div>
    <script src="./js/dashboard.js"></script>
</body>
</html>