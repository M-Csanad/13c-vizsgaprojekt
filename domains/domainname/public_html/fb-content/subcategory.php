<?php
    include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/user_config.php';

    $result = selectData("SELECT subcategory.*, category.name as category_name, category.slug AS category_slug FROM subcategory INNER JOIN category ON subcategory.category_id=category.id WHERE subcategory.id=?", $ids[1], "i");

    if (!$result->isSuccess()) {
        include "http://localhost/fb-functions/error/error-404.html";
    }

    $subcategoryData = $result->message[0];

    $result = selectData("SELECT product.*, product_page.link_slug FROM product_page INNER JOIN product ON product_page.product_id = product.id WHERE product_page.subcategory_id=? ORDER BY product.id ASC;", $ids[1], "i");
    if ($result->isError()) {
        include "http://localhost/fb-functions/error/error-404.html";
        exit();
    }
    
    $products = $result->message;

    $result = selectData("SELECT
            product.id AS product_id,
            MAX(CASE WHEN image.uri LIKE '%thumbnail%' THEN image.uri END) AS thumbnail_image,
            MAX(CASE 
                WHEN image.uri LIKE '%vertical%' THEN image.uri
                WHEN image.uri NOT LIKE '%vertical%' AND image.uri NOT LIKE '%thumbnail%' THEN image.uri 
                END) AS secondary_image
        FROM 
            image
        INNER JOIN 
            product_image ON product_image.image_id = image.id
        INNER JOIN 
            product ON product_image.product_id = product.id
        INNER JOIN 
            product_page ON product_page.product_id = product.id
        WHERE product_page.subcategory_id=?
        GROUP BY 
            product.id, product.name
        ORDER BY product.id ASC;", $ids[1], 'i');

    if ($result->isError()) {
        include "http://localhost/fb-functions/error/error-404.html";
        exit();
    }
    $images = $result->message;

    // A termékekhez hozzácsatoljuk a képeket
    foreach ($products as $index=>$product) {
        // Mivel a termékek is és a képek is növekvő sorrendbe vannak
        // rendezve a product.id alapján, így a lista index-szel
        // is el tudjuk érni a hozzá tartozó képeket
        $products[$index]["thumbnail_image"] = preg_replace('/\.[a-zA-Z0-9]+$/', '', $images[$index]["thumbnail_image"]);
        $products[$index]["secondary_image"] = preg_replace('/\.[a-zA-Z0-9]+$/', '', $images[$index]["secondary_image"]);
    }
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
    <link rel="stylesheet" href="/fb-content/assets/css/footer.css" media="all" />
    
    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer src="/fb-content/assets/js/scrollbar.js"></script>
    <script defer type="module" src="/fb-content/assets/js/filterwindow.js"></script>
    <script defer type="module" src="/fb-content/fb-subcategories/js/subcategory.js"></script>
    <script defer src="/fb-content/assets/js/autogenerate__footer.js"></script>
    
    <!--ionicons-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
<div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div></div>

    <header style="height: 100px;">
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
        <div class="products-wrapper">
            <section class="products">
                <div class="topbar-wrapper">
                    <div class="info">
                        <a class="category" href="/<?= htmlspecialchars($subcategoryData["category_slug"]); ?>">
                            <?= htmlspecialchars($subcategoryData["category_name"]); ?>
                        </a>
                        <header>
                            <?= htmlspecialchars($subcategoryData["name"]); ?> <span style="font-size: 18px; color: #dddddd; font-family: Roboto">- <?= htmlspecialchars($subcategoryData['product_count']); ?> termék</span>
                        </header>
                        <div class="subname">
                            <?= htmlspecialchars($subcategoryData["subname"]); ?>
                        </div>
                    </div>
                    <div class="buttons">
                        <button class="filter-open">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                                <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                            </svg>
                            Szűrés
                        </button>
                        <button class="sort-open">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-filter-right" viewBox="0 0 16 16">
                                <path d="M14 10.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 .5-.5m0-3a.5.5 0 0 0-.5-.5h-7a.5.5 0 0 0 0 1h7a.5.5 0 0 0 .5-.5m0-3a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0 0 1h11a.5.5 0 0 0 .5-.5"/>
                            </svg>
                            Rendezés
                        </button>
                    </div>
                </div>
                <div class="cards">
                    <?php foreach ($products as $product): ?>
                        <div class="card">
                            <div class="card-image">
                                <a href="/<?= htmlspecialchars($product["link_slug"]); ?>">
                                    <?php $resolutions = [1920, 1440, 1024, 768]; ?>
                                    <picture>
                                        <?php foreach ($resolutions as $index=>$resolution): ?>
                                            <source type="image/avif" srcset="<?= $product["thumbnail_image"] ?>-<?= $resolution ?>px.avif 1x" media="(min-width: <?= $resolution ?>px)">
                                            <source type="image/webp" srcset="<?= $product["thumbnail_image"] ?>-<?= $resolution ?>px.webp 1x" media="(min-width: <?= $resolution ?>px)">
                                            <source type="image/jpeg" srcset="<?= $product["thumbnail_image"] ?>-<?= $resolution ?>px.jpg 1x" media="(min-width: <?= $resolution ?>px)">
                                        <?php endforeach; ?>
                                        <!-- Fallback -->
                                        <img 
                                        src="<?= $product["thumbnail_image"] ?>-<?= end($resolutions) ?>px.webp" 
                                        alt="<?= htmlspecialchars($product['name']) ?>" 
                                        loading="lazy"
                                        sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 33vw">
                                    </picture>
                                    <picture class="secondary">
                                        <?php foreach ($resolutions as $index=>$resolution): ?>
                                            <source type="image/avif" srcset="<?= $product["secondary_image"] ?>-<?= $resolution ?>px.avif 1x" media="(min-width: <?= $resolution ?>px)">
                                            <source type="image/webp" srcset="<?= $product["secondary_image"] ?>-<?= $resolution ?>px.webp 1x" media="(min-width: <?= $resolution ?>px)">
                                            <source type="image/jpeg" srcset="<?= $product["secondary_image"] ?>-<?= $resolution ?>px.jpg 1x" media="(min-width: <?= $resolution ?>px)">
                                        <?php endforeach; ?>
                                        <!-- Fallback -->
                                        <source type="image/jpeg" srcset="<?= $product["secondary_image"] ?>.jpg 1x" media="(min-width: 0px)">
                                        <img 
                                        src="<?= $product["secondary_image"] ?>.jpg" 
                                        alt="<?= htmlspecialchars($product['name']) ?>" 
                                        loading="lazy"
                                        sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 33vw">
                                    </picture>
                                </a>
                            <div class="button-wrapper">
                                <button class="quick-add" id="<?= htmlspecialchars(format_str($product["name"])); ?>">
                                <div>Kosárba</div>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-bag"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                    d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"
                                    />
                                </svg>
                                </button>
                            </div>
                            </div>
                            <div class="card-body">
                            <div class="name" title="<?= htmlspecialchars($product["name"]); ?>">
                                <?= htmlspecialchars($product["name"]); ?>
                            </div>
                            <div class="price" aria-label="Ár">
                                <span class="price-value">
                                    <?= htmlspecialchars($product["unit_price"]); ?>
                                </span>
                                <span class="price-currency">Ft</span>
                            </div>
                            <div class="review-stars stars" data-rating="3.5"></div>
                            <div class="review-count">123 értékelés</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
    <footer id="fb-footer"></footer>

    <div id="scrollBar">
        <div id="scrollStatus"></div>
    </div>
</body>
</html>