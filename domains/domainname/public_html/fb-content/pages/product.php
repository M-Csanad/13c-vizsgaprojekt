<?php
    include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
    include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/init.php';

    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $segments = explode('/', $uri); // 0: category, 1: subcategory, 2: product

    // Termék és termékoldal adatainak lekérése
    $result = selectData("SELECT product.*, product_page.id as page_id, product_page.created_at, product_page.last_modified, product_page.page_title, product_page.page_content, category.name AS category_name, subcategory.name AS subcategory_name FROM product_page 
        INNER JOIN product ON product_page.product_id=product.id 
        INNER JOIN category ON product_page.category_id=category.id 
        INNER JOIN subcategory ON product_page.subcategory_id=subcategory.id 
        WHERE category.name LIKE ? AND subcategory.name LIKE ? AND product.name=?", array_map(function($e) { return reverse_format_str($e); }, $segments), "sss");
    
    // Ha nem sikeres, akkor 404 oldal betöltése
    if (!typeOf($result, "SUCCESS")) {
        http_response_code(404);
        include $_SERVER["DOCUMENT_ROOT"] . "/fb-content/pages/error-404.html";
        exit;
    }
    $product = $result["message"][0];
    

    // Termékképek lekérése
    $result = selectData("SELECT image.uri FROM image INNER JOIN product_image ON product_image.image_id=image.id WHERE product_image.product_id=?", $product["id"], "i");
    
    // Ha nem sikeres, akkor 404 oldal betöltése
    if (!typeOf($result, "SUCCESS")) {
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-content/pages/error-404.html";
      exit;
    }
    $images = $result["message"];



    // Címkék lekérése
    $result = selectData("SELECT tag.icon_uri, tag.name FROM tag INNER JOIN product_tag ON product_tag.tag_id=tag.id WHERE product_tag.product_id=?", $product["id"], "i");
    
    // Ha nem sikeres, akkor 404 oldal betöltése
    if (typeOf($result, "ERROR")) {
      logError("Sikertelen termék címke lekérdezés: ".json_encode($result), "productpage.log", $_SERVER["DOCUMENT_ROOT"]."/13c-vizsgaprojekt/.logs");
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-content/pages/error-404.html";
      exit;
    }
    $tags = ($result["type"]=="EMPTY") ? "Nincsenek termékhez csatolt címkék." : $result["message"];

    $result = selectData("SELECT * FROM health_effect INNER JOIN product_health_effect ON product_health_effect.health_effect_id=health_effect.id WHERE product_health_effect.product_id=? AND health_effect.benefit=1;", $product['id'], "i");
    if (typeOf($result, "ERROR")) {
      logError("Sikertelen termék hatások lekérdezés: ".json_encode($result), "productpage.log", $_SERVER["DOCUMENT_ROOT"]."/13c-vizsgaprojekt/.logs");
      http_response_code(404);
      include $_SERVER["DOCUMENT_ROOT"] . "/fb-content/pages/error-404.html";
      exit;
    }

    $benefits = $result["message"];
  
    // Hasonló termékek lekérése
?>
<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $product["name"]; ?></title>

    <link rel="prefetch" href="../../fb-content/assets/media/images/logos/herbalLogo_mini_white.png" as="image" />
    <link rel="stylesheet" href="../../fb-content/pages/assets/css/product.css" />
    <link rel="stylesheet" href="../../fb-content/assets/css/footer.css">
    <link
      rel="preload"
      href="../../fb-content/pages/assets/fonts/PlayfairDisplay-VariableFont_wght.woff2"
      as="font"
      type="font/woff2"
      crossorigin="anonymous"
    />
    <link
      rel="preload"
      href="../../fb-content/pages/assets/fonts/Roboto-Regular.woff2"
      as="font"
      type="font/woff2"
      crossorigin="anonymous"
    />

    <script defer src="../../fb-content/pages/assets/js/product.js"></script>
    <link
      rel="stylesheet"
      href="https://unpkg.com/lenis@1.1.14/dist/lenis.css"
    />
    <script
      defer
      src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js"
    ></script>
    <script defer src="../../fb-content/pages/assets/js/lenis.js"></script>
    <script defer src="../../fb-content/pages/assets/js/loader.js"></script>
    <script defer src="../../fb-content/pages/assets/js/scrollbar.js"></script>

    <style>
      body.loading {
        overflow: hidden;
        margin: 0;
      }
      #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: #1d1d1d; /* Change to desired color */
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        font-family: Arial, sans-serif;
        font-size: 2rem;
        z-index: 9999;
      }

      .loader-message > .title {
        color: #ffffff;
        font-size: 60px;
        font-family: Georgia, "Times New Roman", Times, serif;
        margin-top: 20px;
        letter-spacing: 1px;
        opacity: 0;
        animation: fadeInText 0.5s forwards;
      }

      .loader-message > .content {
        color: #dfdfdf;
        font-size: 30px;
        font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
        margin-top: 20px;
        letter-spacing: 1px;
        opacity: 0;
        text-align: center;
        animation: fadeInText 1s 0.5s forwards, pulse 2s 1.5s infinite;
      }

      @keyframes pulse {
        0%,
        100% {
          scale: 1;
          opacity: 1;
        }

        50% {
          scale: 0.9;
          opacity: 0;
        }
      }

      @keyframes fadeInText {
        0% {
          opacity: 0;
          transform: translateY(15px);
        }

        100% {
          opacity: 1;
          transform: translateY(0);
        }
      }
    </style>
  </head>
  <body>
    <div id="loadingOverlay">
      <div class="loader-logo">
        <div class="loader-logo-inner"></div>
      </div>
      <div class="loader-message">
        <div class="title">Florens Botanica</div>
        <div class="content">Betöltés...</div>
      </div>
    </div>
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
          <a href="<?= htmlspecialchars(ROOT_URL); ?>/" class="breadcrumb-item">Főoldal</a>
          <div class="breadcrumb-splitter">/</div>
          <a href="<?= htmlspecialchars(ROOT_URL."/".format_str($product["category_name"])); ?>" class="breadcrumb-item"><?= htmlspecialchars($product["category_name"]); ?></a>
          <div class="breadcrumb-splitter">/</div>
          <a href="<?= htmlspecialchars(ROOT_URL."/".format_str($product["category_name"])."/".format_str($product["subcategory_name"])); ?>" class="breadcrumb-item"><?= htmlspecialchars($product["subcategory_name"]); ?></a>
          <div class="breadcrumb-splitter">/</div>
        </div>
        <div class="inline-container">
          <div class="price" aria-label="Ár">
            <span class="price-value"><?= htmlspecialchars($product["unit_price"]); ?></span>
            <span class="price-currency">Ft</span>
          </div>
          <div
            class="avg-review"
            aria-label="Átlagos értékelés"
            data-rating="4.7"
            data-reviews="125"
          ></div>
        </div>
        <button class="add-to-cart">
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
            <p>
              <?= $product["description"]; ?>
            </p>
            <p>
                <?= $product["page_content"]; ?>
            </p>
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
            <ul>
              <li>
                TODO
              </li>
            </ul>
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
            <div>TODO</div>
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
              <!-- <div class="tag">
                <img
                  src="./gluten-free.png"
                  alt="Glutén mentes"
                  class="tag-icon"
                  draggable="false"
                />
              </div> -->
            </div>
          </div>
        </section>
      </section>
    </main>
    <section class="reviews">
      <header class="title">Értékelések</header>
      <div class="review-container">
        <div class="review">
          <div class="review-head">
            <div class="review-info">
              <div class="user">
                <div class="profile-pic">
                  <img
                    src="https://ui-avatars.com/api/?name=Blank+Máté&background=9CB5A6&bold=true&format=svg"
                    alt=""
                  />
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-check-circle-fill"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"
                    />
                  </svg>
                </div>
                <div class="profile-info">
                  <div class="name">Blank Máté</div>
                  <div class="verified">Hitelesített vásárló</div>
                </div>
              </div>
              <div class="stars-title">
                <div class="review-stars stars" data-rating="5"></div>
                <div class="review-title">Nagyon elégedett vagyok!</div>
              </div>
            </div>
            <div class="date">2024.12.08</div>
          </div>
          <div class="review-body">
            <div class="review-text">
              Az acai por fantasztikus kiegészítője lett a reggeli rutinomnak!
              Az energiaszintemet és az általános közérzetemet is javította,
              mióta használom. Csak ajánlani tudom mindenkinek!
            </div>
          </div>
        </div>
        <div class="review">
          <div class="review-head">
            <div class="review-info">
              <div class="user">
                <div class="profile-pic">
                  <img
                    src="https://ui-avatars.com/api/?name=Blank+Máté&background=9CB5A6&bold=true&format=svg"
                    alt=""
                  />
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-check-circle-fill"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"
                    />
                  </svg>
                </div>
                <div class="profile-info">
                  <div class="name">Blank Máté</div>
                  <div class="verified">Hitelesített vásárló</div>
                </div>
              </div>
              <div class="stars-title">
                <div class="review-stars stars" data-rating="4.3"></div>
                <div class="review-title">Egész jó</div>
              </div>
            </div>
            <div class="date">2024.12.08</div>
          </div>
          <div class="review-body">
            <div class="review-text">
              Az acai por fantasztikus kiegészítője lett a reggeli rutinomnak!
              Az energiaszintemet és az általános közérzetemet is javította,
              mióta használom. Csak ajánlani tudom mindenkinek!
            </div>
          </div>
        </div>
        <div class="review">
          <div class="review-head">
            <div class="review-info">
              <div class="user">
                <div class="profile-pic">
                  <img
                    src="https://ui-avatars.com/api/?name=Blank+Máté&background=9CB5A6&bold=true&format=svg"
                    alt=""
                  />
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-check-circle-fill"
                    viewBox="0 0 16 16"
                  >
                    <path
                      d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"
                    />
                  </svg>
                </div>
                <div class="profile-info">
                  <div class="name">Blank Máté</div>
                  <div class="verified">Hitelesített vásárló</div>
                </div>
              </div>
              <div class="stars-title">
                <div class="review-stars stars" data-rating="3.5"></div>
                <div class="review-title">Elmegy</div>
              </div>
            </div>
            <div class="date">2024.12.08</div>
          </div>
          <div class="review-body">
            <div class="review-text">
              Az acai por fantasztikus kiegészítője lett a reggeli rutinomnak!
              Az energiaszintemet és az általános közérzetemet is javította,
              mióta használom. Csak ajánlani tudom mindenkinek!
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="recommendations">
      <header class="title">Hasonló termékek</header>
      <div class="products-wrapper">
        <div class="products">
          <!-- <div class="card">
            <div class="card-image">
              <img src="./product-image/image1.jpg" alt="" />
              <div class="button-wrapper">
                <button class="quick-add">
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
              <div class="name">Acai őrlemény</div>
              <div class="price" aria-label="Ár">
                <span class="price-value">8999</span>
                <span class="price-currency">Ft</span>
              </div>
              <div class="review-stars stars" data-rating="3.5"></div>
              <div class="review-count">123 értékelés</div>
            </div>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="./product-image/image1.jpg" alt="" />
              <div class="button-wrapper">
                <button class="quick-add">
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
              <div class="name">Acai őrlemény</div>
              <div class="price" aria-label="Ár">
                <span class="price-value">8999</span>
                <span class="price-currency">Ft</span>
              </div>
              <div class="review-stars stars" data-rating="3.5"></div>
              <div class="review-count">123 értékelés</div>
            </div>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="./product-image/image1.jpg" alt="" />
              <div class="button-wrapper">
                <button class="quick-add">
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
              <div class="name">Acai őrlemény</div>
              <div class="price" aria-label="Ár">
                <span class="price-value">8999</span>
                <span class="price-currency">Ft</span>
              </div>
              <div class="review-stars stars" data-rating="3.5"></div>
              <div class="review-count">123 értékelés</div>
            </div>
          </div>
        </div> -->
      </div>
      <div class="product-navigator">
        <div class="navigator-button navigator-left">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            class="bi bi-chevron-left"
            viewBox="0 0 16 16"
          >
            <path
              fill-rule="evenodd"
              d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"
            />
          </svg>
        </div>
        <div class="navigator-progress">
          <div class="progressbar"></div>
        </div>
        <div class="navigator-button navigator-right">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            fill="currentColor"
            class="bi bi-chevron-right"
            viewBox="0 0 16 16"
          >
            <path
              fill-rule="evenodd"
              d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"
            />
          </svg>
        </div>
      </div>
    </section>

    <div class="divider-flower">
      <div class="hr"></div>
      <!-- SVG by orchidart (www.freepik.com) -->
      <img src="../../fb-content/pages/assets/images/flower.svg" alt="Oldalzáró virág" draggable="false" />
      <div class="hr"></div>
    </div>

    <div id="top-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5"/>
        </svg>
    </div>
    <div id="scrollBar">
        <div id="scrollStatus"></div>
    </div>
  </body>
</html>
