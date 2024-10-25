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
    <?php include_once "./auth/init.php"; ?>
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
                            <select name="type" id="type">
                                <option value="main">Főkategória</option>
                                <option value="sub">Alkategória</option>
                            </select>
                        </div>
                        <div class="inline-input" hidden>
                            <label for="parent_category">Főkategória</label>
                            <select name="parent_category" id="parent_category">
                                <?php

                                ?>
                            </select>
                        </div>
                        <div class="inline-input">
                            <label for="category">Főkategória</label>
                            <input type="text" name="category" id="category" required>
                        </div>
                        <div class="inline-input">
                            <label for="thumbnail_image">Borítókép</label>
                            <input type="file" name="thumbnail_image" id="thumbnail_image" required accept="image/png, image/jpeg">
                        </div>
                    </div>
                    <input type="submit" value="Felvitel" class="form-submit-primary" name='felvitel'>
                </form>
            </div>
            </section>
        </div>
    </div>

    <!----------------------------- Új termék felvitele ---------------------------->
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
                            <div class="inline-input">
                                <label for="thumbnail_image">Borítókép</label>
                                <input type="file" name="thumbnail_image" id="thumbnail_image" required accept="image/png, image/jpeg">
                            </div>
                            <div class="inline-input">
                                <label for="product_images">Termékképek</label>
                                <input type="file" name="product_images[]" id="product_images" multiple required accept="image/png, image/jpeg">
                            </div>
                            <div class="inline-input">
                                <label for="content">Tartalom</label>
                                <input type="text" name="content" id="content" required>
                            </div>
                        </div>
                        <input type="submit" value="Felvitel" class="form-submit-primary" name='felvitel'>
                    </form>
                </div>
            </section>
            <!------------------------------- Termék törlése ------------------------------->
            <section>
                <div class="section-title">Termék törlése</div>
                <div class="section-body">
                    <form method="POST">
                        <div class="input-select">
                            <label for="termekNev">Termék kiválasztása</label>
                            <select name="termek" id="termek">
                                <option value=""></option>
                            </select>
                        </div>
                        <input type="submit" value="Törlés" class="form-submit-danger" name='torles'>
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
                            <div class="inline-input">
                                <label for="thumbnail_image">Borítókép</label>
                                <input type="file" name="thumbnail_image" id="thumbnail_image" required accept="image/png, image/jpeg">
                            </div>
                            <div class="inline-input">
                                <label for="product_images">Termékképek</label>
                                <input type="file" name="product_images[]" id="product_images" multiple required accept="image/png, image/jpeg">
                            </div>
                            <div class="inline-input">
                                <label for="content">Tartalom</label>
                                <input type="text" name="content" id="content" required>
                            </div>
                        </div>
                        <input type="submit" value="Módosítás" class="form-submit-primary" name='modositas'>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <div>
        <?php

            if (isset($_POST['felvitel'])) {
                include_once "./auth/product_management_functions.php";

                unset($_POST['felvitel']);
                $successfulUpload = uploadProduct();

                if ($successfulUpload) {
                    echo "Sikeres feltöltés!";
                }
                else {
                    echo "<div class='error'>Sikertelen feltöltés!</div>";
                }
            }

        ?>
    </div>
    <script src="./js/dashboard.js"></script>
</body>
</html>