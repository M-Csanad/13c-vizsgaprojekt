<?php
    include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/init.php';
    include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/review_functions.php';
    include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/user_config.php';

    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $uri); // 0: category, 1: subcategory, 2: product
    $slug = implode('/', $segments);


    // Termék és termékoldal adatainak lekérése
    $result = selectData("SELECT product.*, product_page.id as page_id, product_page.created_at, product_page.last_modified, product_page.page_title, product_page.page_content, product_page.category_id, category.name AS category_name, subcategory.name AS subcategory_name FROM product_page INNER JOIN product ON product_page.product_id=product.id INNER JOIN category ON product_page.category_id=category.id INNER JOIN subcategory ON product_page.subcategory_id=subcategory.id WHERE product_page.link_slug LIKE ?", [$slug], "s");

    // Ha nem sikeres, akkor 404 oldal betöltése
    if (!$result->isSuccess()) {
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-functions/error/error-404.html";
      exit;
    }
    $product = $result->message[0];


    // Termékképek lekérése
    $result = selectData("SELECT image.uri FROM image INNER JOIN product_image ON product_image.image_id=image.id WHERE product_image.product_id=?", $product["id"], "i");

    // Ha nem sikeres, akkor 404 oldal betöltése
    if (!$result->isSuccess()) {
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-content/error-404.html";
      exit;
    }
    $images = $result->message;


    // Címkék lekérése
    $result = selectData("SELECT tag.icon_uri, tag.name FROM tag INNER JOIN product_tag ON product_tag.tag_id=tag.id WHERE product_tag.product_id=?", $product["id"], "i");

    // Ha nem sikeres, akkor 404 oldal betöltése
    if ($result->isError()) {
      logError("Sikertelen termék címke lekérdezés: " . json_encode($result), "productpage.log", $_SERVER["DOCUMENT_ROOT"] . "/../../../.logs");
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-functions/error/error-404.html";
      exit;
    }
    $tags = ($result->type == "EMPTY") ? "Nincsenek termékhez csatolt címkék." : $result->message;


    // Egészségügyi hatások lekérdezése
    $result = selectData("SELECT * FROM health_effect INNER JOIN product_health_effect ON product_health_effect.health_effect_id=health_effect.id WHERE product_health_effect.product_id=?", $product['id'], "i");

    if ($result->isError()) {
      logError("Sikertelen termék hatások lekérdezés: " . json_encode($result), "productpage.log", $_SERVER["DOCUMENT_ROOT"] . "/../../../.logs");
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-functions/error/error-404.html";
      exit;
    }

    if (is_array($result->message)) {
      $benefits = array_filter($result->message, function ($e) {return $e["benefit"] == 1;});
      $side_effects = array_filter($result->message, function ($e) {return $e["benefit"] == 0;});
    }

    // Értékelések lekérdezése
    $reviewStats = getReviewStats($product['id']);
    if ($reviewStats->isError()) {
      logError("Sikertelen értékelés lekérdezés: " . json_encode($reviewStats), "productpage.log", $_SERVER["DOCUMENT_ROOT"] . "/../../../.logs");
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-functions/error/error-404.html";
      exit;
    }
    $reviewStats = $reviewStats->message;

    // Hasonló termékek lekérése
    $relatedProducts = null;

    // Ugyanabban az alkatrgóriában lévő termékek lekérése
    $limit = 6;
    $relatedProducts = array();

    // Először ugyanabból az alkategóriából gyűjtünk termékeket
    $result = selectData(
      "SELECT DISTINCT p.*, pp.link_slug,
      MAX(CASE WHEN i.uri LIKE '%thumbnail%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '') END) AS thumbnail_image,
      MAX(CASE
      WHEN i.uri LIKE '%vertical%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '')
      WHEN i.uri NOT LIKE '%vertical%' AND i.uri NOT LIKE '%thumbnail%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '')
      END) AS secondary_image,
      COALESCE(AVG(r.rating), 0) as avg_rating,
      COUNT(DISTINCT r.id) as review_count
      FROM product p
      INNER JOIN product_page pp ON p.id = pp.product_id
      LEFT JOIN product_image pi ON p.id = pi.product_id
      LEFT JOIN image i ON pi.image_id = i.id
      LEFT JOIN review r ON p.id = r.product_id
      WHERE pp.subcategory_id = ? AND p.id != ?
      GROUP BY p.id
      ORDER BY p.name
      LIMIT ?",
      [$ids[1], $product['id'], $limit - count($relatedProducts)],
      "iii"
    );

    if ($result->isSuccess() && !$result->isEmpty()) {
      $relatedProducts = array_merge($relatedProducts, $result->message);
    }

    // Ha még kell, akkor ugyanabból a kategóriából gyűjtünk termékeket
    if (count($relatedProducts) < $limit) {
      $result = selectData(
      "SELECT DISTINCT p.*, pp.link_slug,
      MAX(CASE WHEN i.uri LIKE '%thumbnail%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '') END) AS thumbnail_image,
      MAX(CASE
        WHEN i.uri LIKE '%vertical%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '')
        WHEN i.uri NOT LIKE '%vertical%' AND i.uri NOT LIKE '%thumbnail%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '')
      END) AS secondary_image,
      COALESCE(AVG(r.rating), 0) as avg_rating,
      COUNT(DISTINCT r.id) as review_count
      FROM product p
      INNER JOIN product_page pp ON p.id = pp.product_id
      LEFT JOIN product_image pi ON p.id = pi.product_id
      LEFT JOIN image i ON pi.image_id = i.id
      LEFT JOIN review r ON p.id = r.product_id
      WHERE pp.category_id = ? AND p.id != ?
      AND p.id NOT IN (" . implode(',', array_map(function($p) { return $p['id']; }, $relatedProducts)) . ")
      GROUP BY p.id
      ORDER BY p.name
      LIMIT ?",
      [$product['category_id'], $product['id'], $limit - count($relatedProducts)],
      "iii"
      );

      if ($result->isSuccess() && !$result->isEmpty()) {
      $relatedProducts = array_merge($relatedProducts, $result->message);
      }
    }

    // Ha még mindig kell, akkor bármely termék közül keresünk
    if (count($relatedProducts) < $limit) {
      $result = selectData(
      "SELECT DISTINCT p.*, pp.link_slug,
      MAX(CASE WHEN i.uri LIKE '%thumbnail%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '') END) AS thumbnail_image,
      MAX(CASE
        WHEN i.uri LIKE '%vertical%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '')
        WHEN i.uri NOT LIKE '%vertical%' AND i.uri NOT LIKE '%thumbnail%' THEN REGEXP_REPLACE(i.uri, '\\.[^.]+$', '')
      END) AS secondary_image,
      COALESCE(AVG(r.rating), 0) as avg_rating,
      COUNT(DISTINCT r.id) as review_count
      FROM product p
      INNER JOIN product_page pp ON p.id = pp.product_id
      LEFT JOIN product_image pi ON p.id = pi.product_id
      LEFT JOIN image i ON pi.image_id = i.id
      LEFT JOIN review r ON p.id = r.product_id
      WHERE p.id != ?
      AND p.id NOT IN (" . (empty($relatedProducts) ? '-1' : implode(',', array_map(function($p) { return $p['id']; }, $relatedProducts))) . ")
      GROUP BY p.id
      ORDER BY p.name
      LIMIT ?",
      [$product['id'], $limit - count($relatedProducts)],
      "ii"
      );

      if ($result->isSuccess() && !$result->isEmpty()) {
      $relatedProducts = array_merge($relatedProducts, $result->message);
      }
    }

    // Rendezzük a termékeket név szerint
    usort($relatedProducts, function($a, $b) {
      return $a['name'] <=> $b['name'];
    });

?>
<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $product["name"]; ?></title>

    <link rel="prefetch" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white.png" as="image" />
    <link rel="preload" href="/fb-content/assets/font/PlayfairDisplay-VariableFont_wght.woff2" as="font" type="font/woff2" crossorigin="anonymous" />
    <link rel="preload" href="/fb-content/assets/font/Roboto-Regular.woff2" as="font" type="font/woff2" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.14/dist/lenis.css" />
    <link rel="stylesheet" href="/fb-content/fb-products/css/product.css" />
    <link rel="stylesheet" href="/fb-content/fb-products/css/reviewform.css">
    <link rel="stylesheet" href="/fb-content/assets/css/footer.css">
    <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">
    <link rel="icon" href="/fb-content/assets/media/images/logos/herbalLogo_mini_white2.png" type="image/x-icon">
    <link rel="stylesheet" href="/fb-content/assets/css/footer.css" media="all" />
    <link rel="stylesheet" href="/fb-content/assets/css/font.css" />

    <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js" ></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script defer src="/fb-content/assets/js/page_transition.js"></script>
    <script defer type="module" src="/fb-content/fb-products/js/product.js"></script>
    <script defer src="/fb-content/assets/js/lenis.js"></script>
    <script defer src="/fb-content/assets/js/scrollbar.js"></script>
    <script defer type="module" src="/fb-content/fb-products/js/reviewform.js"></script>
    <script defer src="/fb-content/assets/js/autogenerate__footer.js"></script>

    
  </head>
  <body>
  <div class=transition><div class=transition-background></div><div class=transition-text><div class=hero><div class=char>F</div><div class=char>l</div><div class=char>o</div><div class=char>r</div><div class=char>e</div><div class=char>n</div><div class=char>s</div><div class=char> </div><div class=char>B</div><div class=char>o</div><div class=char>t</div><div class=char>a</div><div class=char>n</div><div class=char>i</div><div class=char>c</div><div class=char>a</div></div><div class=quote><div class=char>"</div><div class=char>A</div><div class=char> </div><div class=char>l</div><div class=char>e</div><div class=char>g</div><div class=char>n</div><div class=char>a</div><div class=char>g</div><div class=char>y</div><div class=char>o</div><div class=char>b</div><div class=char>b</div><div class=char> </div><div class=char>g</div><div class=char>a</div><div class=char>z</div><div class=char>d</div><div class=char>a</div><div class=char>g</div><div class=char>s</div><div class=char>á</div><div class=char>g</div><div class=char> </div><div class=char>a</div><div class=char>z</div><div class=char> </div><div class=char>e</div><div class=char>g</div><div class=char>é</div><div class=char>s</div><div class=char>z</div><div class=char>s</div><div class=char>é</div><div class=char>g</div><div class=char>.</div><div class=char>"</div><div class=char> </div><div class=char>-</div><div class=char> </div><div class=char>V</div><div class=char>e</div><div class=char>r</div><div class=char>g</div><div class=char>i</div><div class=char>l</div><div class=char>i</div><div class=char>u</div><div class=char>s</div></div></div></div>
    <header style="height: 100px;">
        <section id="StickyNavbar_container">
            <?php include __DIR__ . '/assets/navbar.php'; ?>
        </section>
    </header>
    <main class="body-main">
      <div class="image-viewer">
        <div class="wrapper">
          <div class="images">
            <?php foreach ($images as $index=>$image): ?>
              <img src="<?= htmlspecialchars($image["uri"]); ?>" alt="Termékkép" class="image <?= htmlspecialchars(($index == 0) ? "active": "") ?>" style="z-index: <?= htmlspecialchars(($index == 0) ? 100: -1); ?>;" />
            <?php endforeach; ?>
          </div>
          <div class="navigator">
            <div class="navigator-images">
            <?php foreach ($images as $index=>$image): ?>
              <img src="<?= htmlspecialchars($image["uri"]); ?>" alt="Navigációs kép" class="navigator-image" draggable="false"/>
            <?php endforeach; ?>
            </div>
            <div class="navigator-arrows">
              <div class="arrow arrow-left">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="25"
                  height="25"
                  fill="currentColor"
                  class="bi bi-arrow-left"
                  viewBox="0 0 16 16"
                >
                  <path
                    fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"
                  />
                </svg>
              </div>
              <div class="navigator-progress">
                <div class="progressbar"></div>
              </div>
              <div class="arrow arrow-right">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="25"
                  height="25"
                  fill="currentColor"
                  class="bi bi-arrow-right"
                  viewBox="0 0 16 16"
                >
                  <path
                    fill-rule="evenodd"
                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr />
      <section class="product-main">
        <header class="product-name" aria-label="Termék név">
          <?= $product["name"]; ?>
        </header>
        <div class="breadcrumb">
          <a title="Főoldal" href="<?= htmlspecialchars(ROOT_URL); ?>/" class="breadcrumb-item">Főoldal</a>
          <div class="breadcrumb-splitter">/</div>
          <a title="<?= htmlspecialchars($product["category_name"]); ?>" href="<?= htmlspecialchars(ROOT_URL."/".format_str($product["category_name"])); ?>" class="breadcrumb-item"><?= htmlspecialchars($product["category_name"]); ?></a>
          <div class="breadcrumb-splitter">/</div>
          <a title="<?= htmlspecialchars($product["subcategory_name"]); ?>" href="<?= htmlspecialchars(ROOT_URL."/".format_str($product["category_name"])."/".format_str($product["subcategory_name"])); ?>" class="breadcrumb-item"><?= htmlspecialchars($product["subcategory_name"]); ?></a>
          <div class="breadcrumb-splitter">/</div>
        </div>
        <div class="price" aria-label="Ár">
          <span class="price-value"><?= htmlspecialchars($product["unit_price"]); ?></span>
          <span class="price-currency">Ft</span>
        </div>
        <div class="inline-container">
          <?php if ($reviewStats['review_count'] > 0): ?>
            <div
              class="avg-review"
              aria-label="Átlagos értékelés"
              data-rating="<?= htmlspecialchars($reviewStats['average_rating']) ?>"
              data-reviews="<?= htmlspecialchars($reviewStats['review_count']) ?>"
            ></div>
          <?php endif; ?>
          <div class="stock-indicator">
              <div class="stock-info">
                  <div class="stock-count">Készleten: <?= htmlspecialchars($product["stock"]); ?> db</div>
              </div>
              <div class="stock-bar-container">
                  <div class="stock-bar <?= $product["stock"] <= 0 ? 'out-of-stock' : ($product["stock"] > 10 ? 'high' : ($product["stock"] > 5 ? 'medium' : 'low')) ?>"
                        style="width: <?= $product["stock"] <= 0 ? '100' : min(($product["stock"] / 15) * 100, 100) ?>%">
                  </div>
              </div>
              <div class="stock-message <?= $product["stock"] <= 0 ? 'out-of-stock' : ($product["stock"] > 10 ? 'high' : ($product["stock"] > 5 ? 'medium' : 'low')) ?>">
                  <?php if ($product["stock"] <= 0): ?>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      <span>Jelenleg nem elérhető</span>
                  <?php elseif ($product["stock"] > 10): ?>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      <span>Nagy mennyiségben elérhető</span>
                  <?php elseif ($product["stock"] > 5): ?>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                      </svg>
                      <span>Fogyóban van a készlet</span>
                  <?php else: ?>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <span>Utolsó darabok! Siessen, mielőtt elfogy!</span>
                  <?php endif; ?>
              </div>
          </div>
        </div>
        <?php if ($product["stock"] > 0): ?>
        <div class="input-group">
          <label for="product-quantity">
            <span>Mennyiség:</span>
          </label>
          <div class="number-field">
            <div class="number-field-subtract">-</div>
            <input type="text" name="product-quantity" class="product-quantity"
                   placeholder="Darab"
                   max="<?= htmlspecialchars($product['stock']); ?>"
                   min="1"
                   value="1"
                   pattern="[0-9]*">
            <div class="number-field-add">+</div>
          </div>
        </div>
        
        <div class="input-group weight-group">
          <label>
            <span>Nettó súly:</span>
          </label>
          <div class="weight-value">
            <span class="weight"><?= htmlspecialchars($product["net_weight"]); ?></span>
            <span class="weight-unit">g/csomag</span>
          </div>
        </div>
        
        <button class="add-to-cart" <?= $product["stock"] <= 0 ? 'disabled' : '' ?>>
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
        <?php endif; ?>
        <div class="share" aria-label="Megosztás">
          <div>Megosztás</div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            class="bi bi-share"
            viewBox="0 0 16 16"
          >
            <path
              d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5m-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3"
            />
          </svg>
        </div>
        <section class="product-description">
          <header class="section-title">Részletes leírás</header>
          <div class="description">
          <?php foreach (explode("\n", $product["description"]) as $line): ?>
              <p>
                  <?= htmlspecialchars($line); ?>
              </p>
            <?php endforeach; ?>
            <?php foreach (explode("\n", $product["page_content"]) as $line): ?>
              <p>
                  <?= htmlspecialchars($line); ?>
              </p>
            <?php endforeach; ?>
          </div>
          <div class="health-benefits">
            <header class="subtitle">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-heart-pulse"
                viewBox="0 0 16 16"
              >
                <path
                  d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053.918 3.995.78 5.323 1.508 7H.43c-2.128-5.697 4.165-8.83 7.394-5.857q.09.083.176.171a3 3 0 0 1 .176-.17c3.23-2.974 9.522.159 7.394 5.856h-1.078c.728-1.677.59-3.005.108-3.947C13.486.878 10.4.28 8.717 2.01zM2.212 10h1.315C4.593 11.183 6.05 12.458 8 13.795c1.949-1.337 3.407-2.612 4.473-3.795h1.315c-1.265 1.566-3.14 3.25-5.788 5-2.648-1.75-4.523-3.434-5.788-5"
                />
                <path
                  d="M10.464 3.314a.5.5 0 0 0-.945.049L7.921 8.956 6.464 5.314a.5.5 0 0 0-.88-.091L3.732 8H.5a.5.5 0 0 0 0 1H4a.5.5 0 0 0 .416-.223l1.473-2.209 1.647 4.118a.5.5 0 0 0 .945-.049l1.598-5.593 1.457 3.642A.5.5 0 0 0 12 9h3.5a.5.5 0 0 0 0-1h-3.162z"
                />
              </svg>
              <div>Jótékony hatások</div>
            </header>
            <?php if (empty($benefits)): ?>
              <div>Nincsenek jótékony hatások csatolva ehhez a termékhez.</div>
            <?php else: ?>
              <ul>
                <?php foreach ($benefits as $index=>$benefit): ?>
                  <li>
                    <b><?= htmlspecialchars($benefit["name"]); ?></b>: <?= htmlspecialchars($benefit["description"]); ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <div class="precautions">
            <header class="subtitle">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-eye"
                viewBox="0 0 16 16"
              >
                <path
                  d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"
                />
                <path
                  d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"
                />
              </svg>
              <div>Mellékhatások</div>
            </header>
            <?php if (empty($side_effects)): ?>
            <div>Nincsenek mellékhatásai.</div>
            <?php else: ?>
              <ul>
                <?php foreach ($side_effects as $index=>$side_effect): ?>
                  <li>
                    <b><?= htmlspecialchars($side_effect["name"]); ?></b>: <?= htmlspecialchars($side_effect["description"]); ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <div class="tags-container">
            <header class="subtitle">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-tags"
                viewBox="0 0 16 16"
              >
                <path
                  d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"
                />
                <path
                  d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z"
                />
              </svg>
              <div>Címkék</div>
            </header>
            <div class="tags" aria-label="Címkék">
              <?php if (is_array($tags)): ?>
                <?php foreach ($tags as $tag): ?>
                  <div class="tag">
                    <img
                      src="<?= htmlspecialchars($tag["icon_uri"]) ?>"
                      alt="<?= htmlspecialchars($tag["name"]) ?>"
                      class="tag-icon"
                      draggable="false"
                    />
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <?= htmlspecialchars($tags); ?>
              <?php endif; ?>
            </div>
          </div>
        </section>
      </section>
    </main>
    <section class="reviews">
      <header class="title">Értékelések</header>
      <?php if ($isLoggedIn): ?>
        <div class="review-form-container">
          <form class="review-form" action="">
            <?php if ($reviewStats['review_count'] == 0): ?>
              <div class="title">Légy te az első, aki értékeli ezt a terméket!</div>
            <?php else: ?>
              <div class="title">Oszd meg véleményedet velünk!</div>
            <?php endif; ?>
            <div class="review-form-stars grey">
              <div class="star" data-index="0">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-empty.svg" alt="Üres csillag" class="empty active" draggable="false">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-filled.svg" alt="Teli csillag" class="full" draggable="false">
              </div>
              <div class="star" data-index="1">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-empty.svg" alt="Üres csillag" class="empty active" draggable="false">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-filled.svg" alt="Teli csillag" class="full" draggable="false">
              </div>
              <div class="star" data-index="2">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-empty.svg" alt="Üres csillag" class="empty active" draggable="false">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-filled.svg" alt="Teli csillag" class="full" draggable="false">
              </div>
              <div class="star" data-index="3">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-empty.svg" alt="Üres csillag" class="empty active" draggable="false">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-filled.svg" alt="Teli csillag" class="full" draggable="false">
              </div>
              <div class="star" data-index="4">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-empty.svg" alt="Üres csillag" class="empty active" draggable="false">
                  <img src="<?= htmlspecialchars(ROOT_URL)?>/fb-content/fb-products/media/images/star-filled.svg" alt="Teli csillag" class="full" draggable="false">
              </div>
              <input type="hidden" name="stars-input" value="null">
            </div>
            <div class="review-form-title">
              <input type="text" name="review-title" id="review-title" placeholder="Cím">
            </div>
            <div class="review-form-body">
              <textarea name="review-body" id="review-body" placeholder="Leírás"></textarea>
            </div>
            <button class="send-button submit" type="button" disabled>
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
              </svg>
              <div class="success send-feedback">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <div class="send-text">Sikeres küldés!</div>
              </div>
              <div class="unsuccessful send-feedback">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                </svg>
                <div class="send-text">Sikertelen küldés!</div>
              </div>
            </button>
          </form>
        </div>
      <?php else: ?>
        <?php if ($reviewStats['review_count'] == 0): ?>
          <div class="form-subtitle">Még nincsenek értékelések ehhez a termékhez.</div>
        <?php endif; ?>
      <?php endif; ?>
      <?php if ($reviewStats['review_count'] > 0): ?>
        <hr>
        <div class="review-container"></div>
        <?php if ($reviewStats['review_count'] > 3): ?>
        <div class="pagination review-pagination">
          <button class="prev-page-btn" disabled>Előző</button>
          <div class="page-numbers">
          </div>
          <button class="next-page-btn">Következő</button>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </section>
    <section class="recommendations">
      <header class="title">Hasonló termékek</header>
      <div class="products-wrapper">
        <div class="products">
          <?php if (is_array($relatedProducts)): ?>
            <?php foreach ($relatedProducts as $relatedProduct): ?>
              <div class="card" data-product-id="<?= htmlspecialchars($relatedProduct["id"]); ?>">
                <div class="card-image">
                    <a href="/<?= htmlspecialchars($relatedProduct["link_slug"]); ?>">
                        <?php $resolutions = [1920, 1440, 1024, 768]; ?>
                        <picture>
                            <?php foreach ($resolutions as $index=>$resolution): ?>
                                <source type="image/avif" srcset="<?= $relatedProduct["thumbnail_image"] ?>-<?= $resolution ?>px.avif 1x" media="(min-width: <?= $resolution ?>px)">
                                <source type="image/webp" srcset="<?= $relatedProduct["thumbnail_image"] ?>-<?= $resolution ?>px.webp 1x" media="(min-width: <?= $resolution ?>px)">
                                <source type="image/jpeg" srcset="<?= $relatedProduct["thumbnail_image"] ?>-<?= $resolution ?>px.jpg 1x" media="(min-width: <?= $resolution ?>px)">
                            <?php endforeach; ?>
                            <!-- Fallback -->
                            <img
                            src="<?= $relatedProduct["thumbnail_image"] ?>-<?= end($resolutions) ?>px.webp"
                            alt="<?= htmlspecialchars($relatedProduct['name']) ?>"
                            loading="lazy"
                            sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 33vw">
                        </picture>
                        <picture class="secondary">
                            <?php foreach ($resolutions as $index=>$resolution): ?>
                                <source type="image/avif" srcset="<?= $relatedProduct["secondary_image"] ?>-<?= $resolution ?>px.avif 1x" media="(min-width: <?= $resolution ?>px)">
                                <source type="image/webp" srcset="<?= $relatedProduct["secondary_image"] ?>-<?= $resolution ?>px.webp 1x" media="(min-width: <?= $resolution ?>px)">
                                <source type="image/jpeg" srcset="<?= $relatedProduct["secondary_image"] ?>-<?= $resolution ?>px.jpg 1x" media="(min-width: <?= $resolution ?>px)">
                            <?php endforeach; ?>
                            <!-- Fallback -->
                            <source type="image/jpeg" srcset="<?= $relatedProduct["secondary_image"] ?>.jpg 1x" media="(min-width: 0px)">
                            <img
                            src="<?= $relatedProduct["secondary_image"] ?>.jpg"
                            alt="<?= htmlspecialchars($relatedProduct['name']) ?>"
                            loading="lazy"
                            sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 33vw">
                        </picture>
                    </a>
                <div class="button-wrapper">
                    <button class="quick-add"
                            data-product-id="<?= htmlspecialchars($relatedProduct["id"]); ?>"
                            data-product-url="<?= htmlspecialchars($relatedProduct["link_slug"]); ?>">
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
                <div class="name" title="<?= htmlspecialchars($relatedProduct["name"]); ?>">
                    <?= htmlspecialchars($relatedProduct["name"]); ?>
                </div>
                <div class="price" aria-label="Ár">
                    <span class="price-value">
                        <?= htmlspecialchars($relatedProduct["unit_price"]); ?>
                    </span>
                    <span class="price-currency">Ft</span>
                </div>
                <?php if ($relatedProduct["review_count"] > 0): ?>
                    <div class="card-bottom">
                        <div class="review-stars stars" data-rating="<?= htmlspecialchars($relatedProduct["avg_rating"]); ?>"></div>
                        <div class="review-count">
                            <?= htmlspecialchars($relatedProduct["review_count"]) . ' értékelés'; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card-bottom">
                        <div class="review-count">
                            Még nincs értékelve
                        </div>
                    </div>
                <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="product-navigator">
        <div class="navigator-button navigator-left">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
          </svg>
        </div>
        <div class="navigator-progress">
          <div class="progressbar"></div>
        </div>
        <div class="navigator-button navigator-right">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
          </svg>
        </div>
      </div>
    </section>

    <div class="divider-flower">
      <div class="hr"></div>
      <!-- SVG by orchidart (www.freepik.com) -->
      <img src="/fb-content/fb-products/media/images/flower.svg" alt="Oldalzáró virág" draggable="false" />
      <div class="hr"></div>
    </div>

    <div id="top-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5"/>
        </svg>
    </div>
    <footer id="fb-footer"></footer>

  <div id="floatingCart" class="floating-cart">
    <button class="floating-add-to-cart" <?= ($product["stock"] <= 0 ? 'disabled' : '') ?>>
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
      <span>Kosárba</span>
    </button>
  </div>

  <footer id="fb-footer"></footer>

  <div id="scrollBar">
    <div id="scrollStatus"></div>
  </div>

</body>

</html>
