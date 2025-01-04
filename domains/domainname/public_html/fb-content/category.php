<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="http://localhost//fb-content/assets/css/box.css" />
  <link rel="stylesheet" href="http://localhost//fb-content/assets/css/font.css" />
  <link rel="stylesheet" href="http://localhost//fb-content/fb-categories/css/category-cards.css" />
  <link rel="stylesheet" href="/fb-content/assets/css/page_transition.css">

  <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script defer src="/fb-content/assets/js/page_transition.js"></script>
  <title>Category Cards</title>
</head>

<body>
  <div class=transition>
    <div class=transition-text>
      <div class=hero>
        <div class=char>F</div>
        <div class=char>l</div>
        <div class=char>o</div>
        <div class=char>r</div>
        <div class=char>e</div>
        <div class=char>n</div>
        <div class=char>s</div>
        <div class=char> </div>
        <div class=char>B</div>
        <div class=char>o</div>
        <div class=char>t</div>
        <div class=char>a</div>
        <div class=char>n</div>
        <div class=char>i</div>
        <div class=char>c</div>
        <div class=char>a</div>
      </div>
      <div class=quote>
        <div class=char>"</div>
        <div class=char>A</div>
        <div class=char> </div>
        <div class=char>l</div>
        <div class=char>e</div>
        <div class=char>g</div>
        <div class=char>n</div>
        <div class=char>a</div>
        <div class=char>g</div>
        <div class=char>y</div>
        <div class=char>o</div>
        <div class=char>b</div>
        <div class=char>b</div>
        <div class=char> </div>
        <div class=char>g</div>
        <div class=char>a</div>
        <div class=char>z</div>
        <div class=char>d</div>
        <div class=char>a</div>
        <div class=char>g</div>
        <div class=char>s</div>
        <div class=char>á</div>
        <div class=char>g</div>
        <div class=char> </div>
        <div class=char>a</div>
        <div class=char>z</div>
        <div class=char> </div>
        <div class=char>e</div>
        <div class=char>g</div>
        <div class=char>é</div>
        <div class=char>s</div>
        <div class=char>z</div>
        <div class=char>s</div>
        <div class=char>é</div>
        <div class=char>g</div>
        <div class=char>.</div>
        <div class=char>"</div>
        <div class=char> </div>
        <div class=char>-</div>
        <div class=char> </div>
        <div class=char>V</div>
        <div class=char>e</div>
        <div class=char>r</div>
        <div class=char>g</div>
        <div class=char>i</div>
        <div class=char>l</div>
        <div class=char>i</div>
        <div class=char>u</div>
        <div class=char>s</div>
      </div>
    </div>
    <div class="layer layer-0">
      <div class="row-1 transition-row">
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
      </div>
    </div>
    <div class="layer layer-1">
      <div class="row-1 transition-row">
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
      </div>
    </div>
    <div class="layer layer-2">
      <div class="row-1 transition-row">
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
      </div>
    </div>
    <div class="layer layer-3">
      <div class="row-1 transition-row">
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
        <div class=block></div>
      </div>
    </div>
  </div>

  <header>
    <section id="StickyNavbar_container">
      <?php include __DIR__ . '/assets/navbar.php'; ?>
    </section>
  </header>

  <?php
  include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . '/solid_func.php';
  include_once $_SERVER['DOCUMENT_ROOT'] . '/error_logger.php';
  include_once BASE_PATH . '/../../../.ext/db_connect.php';

  function getSubcategoriesByCategory($categoryId)
  {
    $conn = db_connect();

    $sql = "SELECT
                subcategory.id,
                subcategory.name,
                subcategory.thumbnail_image_vertical_uri AS image_uri,
                subcategory.description,
                subcategory.product_count
            FROM subcategory
            WHERE subcategory.category_id = ?
            ORDER BY subcategory.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $subcategories = [];
    while ($row = $result->fetch_assoc()) {
      $subcategories[] = $row;
    }

    $stmt->close();
    db_disconnect($conn);

    return $subcategories;
  }

  function getOtherCategories($currentCategoryId)
  {
    $conn = db_connect();

    $sql = "SELECT
                category.id,
                category.name,
                category.slug,
                category.thumbnail_image_vertical_uri AS image_uri,
                category.description,
                category.product_count
            FROM category
            WHERE category.id != ?
            ORDER BY category.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $currentCategoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while ($row = $result->fetch_assoc()) {
      $categories[] = $row;
    }

    $stmt->close();
    db_disconnect($conn);

    return $categories;
  }

  // Az aktuális főkategória azonosítása az URL slug alapján
  $currentSlug = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
  logError($currentSlug, 'category.log');

  // Lekérdezés az URL-ből származó slug alapján a category ID meghatározására
  function getCategoryIdBySlug($slug)
  {
    $conn = db_connect();

    $sql = "SELECT id, slug FROM category WHERE slug = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();

    $category = null;
    if ($row = $result->fetch_assoc()) {
      $category = $row;
    }

    $stmt->close();
    db_disconnect($conn);

    return $category;
  }

  $currentCategory = getCategoryIdBySlug($currentSlug);
  $currentCategoryId = $currentCategory['id'];
  $currentCategorySlug = $currentCategory['slug'];
  $subcategories = getSubcategoriesByCategory($currentCategoryId);
  $categories = getOtherCategories($currentCategoryId);
  ?>

  <main>
    <section class="Card-subcontainer_main">
      <h1 class="section_headerProductTitle">Fedezd fel ajánlatainkat</h1>

      <div class="flex-block" id="CardSection__subcategory">
        <?php foreach ($subcategories as $subcategory): ?>
          <?php
          $slug = slug_gen($subcategory['name']);
          $url = "/$currentCategorySlug/$slug";
          $fileInfo = pathinfo($subcategory['image_uri']);
          $FileName = $fileInfo['dirname'] . '/' . $fileInfo['filename'];
          $resolutions = [1920, 1440, 1024];
          ?>
          <div class="col-3_inner CardContainer horizontalCard">
            <a href="<?= $url ?>" class="card-link">
              <div class="col-4_inner Card_imageWrapper Card_themeImage">
                <picture>
                  <?php foreach ($resolutions as $resolution): ?>
                    <source type="image/avif" media="(min-width:<?= $resolution ?>px)"
                      srcset="<?= $FileName ?>-<?= $resolution ?>px.avif">
                    <source type="image/webp" media="(min-width:<?= $resolution ?>px)"
                      srcset="<?= $FileName ?>-<?= $resolution ?>px.webp">
                    <source type="image/jpeg" media="(min-width:<?= $resolution ?>px)"
                      srcset="<?= $FileName ?>-<?= $resolution ?>px.jpg">
                  <?php endforeach; ?>
                  <img src="<?= $FileName ?>-<?= end($resolutions) ?>px.webp"
                    alt="<?= htmlspecialchars($subcategory['name']) ?>" loading="lazy">
                </picture>
              </div>
              <div class="col-8_inner Card_body">
                <div class="Card_header">
                  <div class="Card_headerProductCounter"><?= $subcategory['product_count'] ?> products</div>
                  <div class="Card_headerProductTitle"><?= htmlspecialchars($subcategory['name']) ?></div>
                </div>

              </div>
              <div class="card-pattern"></div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="Card-container_main">
      <h1 class="section_headerProductTitle">Nézd meg a többi kategóriát</h1>

      <div class="flex-block" id="CardSection__category">
        <?php foreach ($categories as $category): ?>
          <?php
          $slug = slug_gen($category['slug']);
          $url = "/$slug";
          $fileInfo = pathinfo($category['image_uri']);
          $FileName = $fileInfo['dirname'] . '/' . $fileInfo['filename'];
          $resolutions = [1920, 1440, 1024];
          ?>
          <div class="CardContainer verticalCard">
            <a href="<?= $url ?>" class="card-link">
              <div class="Card_header">
                <div class="Card_headerProductCounter"><?= $category['product_count'] ?> products</div>
                <div class="Card_headerProductTitle"><?= htmlspecialchars($category['name']) ?></div>
              </div>
              <div class="Card_body">
                <p>
                  <?= implode(' ', array_slice(explode(' ', htmlspecialchars($category['description'])), 0, 12)) . '...' ?>
                </p>
              </div>
              <div class="Card_footer">
                <div class="Card_footerLeft">
                  <div class="Card_imageWrapper Card_logoImage">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g id="iconCarrier">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M9 6.75C8.58579 6.75 8.25 6.41421 8.25 6C8.25 5.58579 8.58579 5.25 9 5.25H18C18.4142 5.25 18.75 5.58579 18.75 6V15C18.75 15.4142 18.4142 15.75 18 15.75C17.5858 15.75 17.25 15.4142 17.25 15V7.81066L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L16.1893 6.75H9Z"
                          fill="#ffffff"></path>
                      </g>
                    </svg>
                  </div>
                </div>
                <div class="Card_footerRight">
                  <div class="Card_imageWrapper Card_themeImage">
                    <picture>
                      <?php foreach ($resolutions as $resolution): ?>
                        <source type="image/avif" media="(min-width:<?= $resolution ?>px)"
                          srcset="<?= $FileName ?>-<?= $resolution ?>px.avif">
                        <source type="image/webp" media="(min-width:<?= $resolution ?>px)"
                          srcset="<?= $FileName ?>-<?= $resolution ?>px.webp">
                        <source type="image/jpeg" media="(min-width:<?= $resolution ?>px)"
                          srcset="<?= $FileName ?>-<?= $resolution ?>px.jpg">
                      <?php endforeach; ?>
                      <img src="<?= $FileName ?>-<?= end($resolutions) ?>px.jpg"
                        alt="<?= htmlspecialchars($category['name']) ?>" loading="lazy">
                    </picture>
                  </div>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>



</body>

</html>
