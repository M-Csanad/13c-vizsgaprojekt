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

    <!----------------------------- Új termék felvitele ---------------------------->
    <section>
        <div class="section-title">Új termék felvitele</div>
        <div class="section-body">
            <form method="POST">
                <div class="input-grid">
                    <div class="inline-input">
                        <label for="termekNev">Termék név</label>
                        <input type="text" name="termekNev" id="termekNev">
                    </div>
                    <div class="inline-input">
                        <label for="leiras">Leírás</label>
                        <input type="text" name="leiras" id="leiras">
                    </div>
                    <div class="inline-input">
                        <label for="ar">Egységár</label>
                        <input type="number" name="ar" id="ar">
                    </div>
                    <div class="inline-input">
                        <label for="keszlet">Készlet</label>
                        <input type="number" name="keszlet" id="keszlet">
                    </div>
                    <div class="inline-input">
                        <label for="kategoria">Kategória</label>
                        <input type="text" name="kategoria" id="kategoria">
                    </div>
                    <div class="inline-input">
                        <label for="alkategoria">Alkategória</label>
                        <input type="text" name="alkategoria" id="alkategoria">
                    </div>
                    <div class="inline-input">
                        <label for="boritokep">Borítókép</label>
                        <input type="file" name="boritokep" id="boritokep">
                    </div>
                    <div class="inline-input">
                        <label for="termekkepek">Termékképek</label>
                        <input type="file" name="termekkepek" id="termekkepek" multiple>
                    </div>
                    <div class="inline-input">
                        <label for="tartalom">Tartalom</label>
                        <input type="text" name="tartalom" id="tartalom">
                    </div>
                </div>
                <input type="submit" value="Felvitel" class="form-submit-primary">
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
                <input type="submit" value="Törlés" class="form-submit-danger">
            </form>
        </div>
    </section>

    <!------------------------------ Termék módosítása ----------------------------->
    <section>
        <div class="section-title">Meglévő termék szerkesztése</div>
        <div class="section-body">
            <form method="POST">
                <div class="input-grid">
                    <div class="inline-input">
                        <label for="termekNev">Termék név</label>
                        <input type="text" name="termekNev" id="termekNev">
                    </div>
                    <div class="inline-input">
                        <label for="leiras">Leírás</label>
                        <input type="text" name="leiras" id="leiras">
                    </div>
                    <div class="inline-input">
                        <label for="ar">Egységár</label>
                        <input type="number" name="ar" id="ar">
                    </div>
                    <div class="inline-input">
                        <label for="keszlet">Készlet</label>
                        <input type="number" name="keszlet" id="keszlet">
                    </div>
                    <div class="inline-input">
                        <label for="kategoria">Kategória</label>
                        <input type="text" name="kategoria" id="kategoria">
                    </div>
                    <div class="inline-input">
                        <label for="alkategoria">Alkategória</label>
                        <input type="text" name="alkategoria" id="alkategoria">
                    </div>
                    <div class="inline-input">
                        <label for="boritokep">Borítókép</label>
                        <input type="file" name="boritokep" id="boritokep">
                    </div>
                    <div class="inline-input">
                        <label for="termekkepek">Termékképek</label>
                        <input type="file" name="termekkepek" id="termekkepek" multiple>
                    </div>
                    <div class="inline-input">
                        <label for="tartalom">Tartalom</label>
                        <input type="text" name="tartalom" id="tartalom">
                    </div>
                </div>
                <input type="submit" value="Módosítás" class="form-submit-primary">
            </form>
        </div>
    </section>
    <script src="./js/dashboard.js"></script>
</body>
</html>