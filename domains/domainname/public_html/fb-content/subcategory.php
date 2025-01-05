<?php
    session_start();

    $result = selectData("SELECT subcategory.*, category.name as category_name, category.slug AS category_slug FROM subcategory INNER JOIN category ON subcategory.category_id=category.id WHERE subcategory.id=?", $ids[1], "i");

    if (!typeOf($result, "SUCCESS")) {
        include "http://localhost/fb-functions/error/error-404.html";
    }

    $subcategoryData = $result["message"][0];

    $result = selectData("SELECT product.* FROM product_page INNER JOIN product ON product_page.product_id = product.id WHERE product_page.subcategory_id=?", $ids[1], "i");
    if (typeOf($result, "ERROR")) {
        include "http://localhost/fb-functions/error/error-404.html";
    }
    
    $products = $result["message"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($subcategoryData["name"]); ?></title>

    <link rel="stylesheet" href="/fb-content/fb-subcategories/css/subcategory.css">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />
    <link rel="stylesheet" href="/fb-content/assets/css/footer.css">
    <link rel="stylesheet" href="/fb-content/fb-products/css/reviewform.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="shortcut icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white.png" type="image/x-icon">

    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer src="/fb-content/assets/js/scrollbar.js"></script>
    <script type="module" defer src="/fb-content/assets/js/filterwindow.js"></script>
    <script type="module" defer src="/fb-content/fb-subcategories/js/subcategory.js"></script>
</head>
<body>
    <!-- <div class=transition><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div><div class="layer layer-0"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-1"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-2"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div><div class="layer layer-3"><div class="row-1 transition-row"><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div><div class=block></div></div></div></div> -->

    <header style="height: auto;">
        <section id="StickyNavbar_container">
            <?php include __DIR__ . '/assets/navbar.php'; ?>
        </section>
    </header>

    <main>
        <section class="filters">
            <header>Szűrés</header>
            <div class="product-count">5 termék</div>
            <div class="filter-logo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                </svg>
            </div>
            <div class="filter-group">
                <header>Ár szerint</header>
                <div class="filter" data-target="price">
                    <div class="filter-input">
                        <label for="price_min">Minimum</label>
                        <input type="number" name="price_min" id="price_min">
                    </div>
                    <div class="filter-input">
                        <label for="price_min">Maximum</label>
                        <input type="number" name="price_min" id="price_min">
                    </div>
                </div>
            </div>
            <div class="filter-group">
                <header>Elérhetőség</header>
                <div class="filter" data-target="stock">
                    <div class="filter-input">
                        <input type="checkbox" name="in_stock" id="in_stock">
                        <label for="in_stock">Raktáron</label>
                    </div>
                </div>
            </div>
            <div class="filter-group">
                <header>Címkék</header>
                <div class="filter" data-target="tags">
                    <div class="filter-input">
                        <input type="radio" name="tags" id="tags">
                        <label for="tags">Raktáron</label>
                    </div>
                </div>
            </div>
            <div class="filter-close">
                <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                </svg>
            </div>
            <div class="bottom-buttons">
                <button class="filter-apply">Szűrés</button>
                <button class="filter-clear">Visszaállítás</button>
            </div>
        </section>
        <section class="products">
            <div class="category">
                <?= htmlspecialchars($subcategoryData["category_name"]); ?>
            </div>
            <header>
                <?= htmlspecialchars($subcategoryData["name"]); ?>
            </header>
            <div class="subname">
                <?= htmlspecialchars($subcategoryData["subname"]); ?>
            </div>

            <div class="top-bar">
                <button>Rendezés</button>
                <button class="filter-open">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                        <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                    </svg>
                    Szűrés
                </button>
            </div>
            <div class="cards">
                <?php var_dump($products); ?>
            </div>
        </section>
    </main>

    <div id="scrollBar">
        <div id="scrollStatus"></div>
    </div>
</body>
</html>