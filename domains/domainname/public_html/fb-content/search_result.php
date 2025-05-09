<?php
include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/user_config.php';

$searchQuery = htmlspecialchars($_GET['q'] ?? '');
if (empty($searchQuery)) {
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>Keresési eredmények: <?= htmlspecialchars($searchQuery) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/fb-content/fb-subcategories/css/subcategory.css">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />
    <link rel="stylesheet" href="/fb-content/assets/css/footer.css">
    <link rel="stylesheet" href="/fb-content/fb-products/css/reviewform.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="shortcut icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="stylesheet" href="/fb-content/assets/css/footer.css" media="all" />
    <link rel="stylesheet" href="/fb-content/assets/css/font.css" />
    <link rel="stylesheet" href="/fb-auth/assets/css/inputs.css">
    <link rel="stylesheet" href="/fb-content/assets/css/sortdropdown.css">
    
    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollToPlugin.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer src="/fb-content/assets/js/scrollbar.js"></script>
    <script defer type="module" src="/fb-content/assets/js/filterwindow.js"></script>
    <script defer type="module" src="/fb-content/assets/js/search_page.js"></script>
    <script defer src="/fb-content/assets/js/autogenerate__footer.js"></script>
    

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
            <div class="product-count"></div>
            <div class="filter-logo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                </svg>
            </div>
            <div class="filter-group">
                <header>Ár</header>
                <div class="filter" data-target="price">
                    <div class="input-group-inline">
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="price_min">Minimum</label>
                                <input type="number" name="price_min" id="price_min" required placeholder="A minimum ár" tabindex="1">
                            </div>
                            <div class="message-wrapper">
                                <div class="error-message"></div>
                            </div>
                        </div>
                        <div class="input-group" tabindex="-1">
                            <div class="input-body" tabindex="-1">
                                <label for="price_max">Maximum</label>
                                <input type="number" name="price_max" id="price_max" required placeholder="A maximum ár" tabindex="1">
                            </div>
                            <div class="message-wrapper">
                                <div class="error-message"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter-group">
                <header>Elérhetőség</header>
                <div class="filter" data-target="stock">
                    <div class="checkbox-input">
                        <input type="checkbox" name="in_stock" id="in_stock">
                        <label for="in_stock">Raktáron</label>
                    </div>
                </div>
            </div>
            <div class="filter-group">
                <header>Címkék</header>
                <div class="filter" data-target="tags">
                    <div class="tags" data-lenis-prevent></div>
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
                        <header>
                            Keresési eredmények: <?= htmlspecialchars($searchQuery) ?>
                            <span class="product-count" style="font-size: 18px; color: #dddddd; font-family: Roboto">
                                 találat
                            </span>
                        </header>
                    </div>
                    <div class="buttons">
                        <button class="filter-open">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                                <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                            </svg>
                            Szűrés
                        </button>
                        <!-- JS fogja majd ide generálni a rendezéses gombokat -->
                        <div class="sort-open"></div>
                    </div>
                </div>
                <div class="cards"></div>
                <div class="pagination">
                    <button class="prev-page">Előző</button>
                    <div class="page-numbers"></div>
                    <button class="next-page">Következő</button>
                </div>
            </section>
        </div>
    </main>

    <div id="top-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5"/>
        </svg>
    </div>
    <footer id="fb-footer"></footer>

    <div id="scrollBar">
        <div id="scrollStatus"></div>
    </div>
</body>

</html>
