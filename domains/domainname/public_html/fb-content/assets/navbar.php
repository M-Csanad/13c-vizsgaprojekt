<?php
include_once __DIR__ . '/../../config.php';
$loggerPath = BASE_PATH . '/error_logger.php';
$filePath = BASE_PATH . '/../../../.ext/db_connect.php';



if ($loggerPath === false) {
    throw new Exception("Az error_logger.php fájl nem található.");
}
include_once $loggerPath;
// Ellenőrizzük, hogy a fájl létezik-e
if ($filePath === false || !file_exists($filePath)) {
    logError("Az include fájl nem található: $filePath", 'navbar.log');
    throw new Exception("Nem sikerült betölteni a szükséges fájlt. Nézd meg a naplófájlt a részletekért.");
}
include_once $filePath;

function generateSlug($string)
{
    $unwanted_chars = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'Á' => 'A',
        'É' => 'E',
        'Í' => 'I',
        'Ó' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ú' => 'U',
        'Ü' => 'U',
        'Ű' => 'U'
    ];

    $string = strtr($string, $unwanted_chars); // Ékezetek eltávolítása
    $string = strtolower($string); // Kisbetűsítés
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string); // Speciális karakterek eltávolítása
    $string = preg_replace('/\s+/', '-', $string); // Szóközök helyettesítése kötőjellel
    $result = trim($string, '-'); // Végéről kötőjelek eltávolítása
    return $result;
}

/**
 * Kategória tartalmak lekérése az adatbázisból.
 *
 * @return array A kategóriák tartalma.
 * @throws Exception Ha az adatbázis-kapcsolat nem sikerül.
 */
function getCategoryContent()
{
    $conn = db_connect(); // Új funkció használata

    $sql = "SELECT
                category.name AS category_name,
                category.thumbnail_image_vertical_uri AS category_image,
                subcategory.name AS subcategory_name,
                subcategory.category_id AS category_id
            FROM category
            LEFT JOIN subcategory ON category.id = subcategory.category_id";


    $result = $conn->query($sql);
    $category_content = [];

    if ($result && $result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $category_name = $row['category_name'];
            $subcategory_name = $row['subcategory_name'];
            $category_image = $row['category_image'];


            $category_slug = generateSlug($category_name);
            $category_url = "./" . $category_slug . "/";

            if (!isset($category_content[$category_name])) {
                $category_content[$category_name] = [
                    'title' => $category_name,
                    'url' => $category_url,
                    'img' => $category_image,
                    'subcategories' => []
                ];

            }

            if ($subcategory_name) {
                $subcategory_slug = generateSlug($subcategory_name);
                $subcategory_url = "./" . $category_slug . "/" . $subcategory_slug;

                $category_content[$category_name]['subcategories'][] = [
                    'name' => $subcategory_name,
                    'url' => $subcategory_url
                ];

            }
        }
    } else {
        logError("SQL lekérdezés nem adott eredményt", 'navbar.log');
    }

    db_disconnect($conn); // Kapcsolat lezárása


    return array_values($category_content); // Az asszociatív indexeket numerikusra váltja
}

$category_content = getCategoryContent();

// Navigációs elemek
$base_url = ".";
$logo_url = htmlspecialchars("$base_url/fb-content/assets/media/images/logos/herbalLogo_mini_white.png");
$menu_items = [
    ['name' => 'Categories', 'id' => 'fb-navlink-category', 'url' => '#'],
    ['name' => 'About us', 'url' => "$base_url/about-us/"],
    ['name' => 'Privacy Policy', 'url' => "$base_url/privacy-policy/"]
];



?>


<nav id="fb-navbar" class="fb-sticky" data-category='<?= json_encode($category_content); ?>'>
    <div id="fb-navTopWrapper" class="fb-nav-links-wrapper">
        <a href="<?= htmlspecialchars($base_url) ?>"><img class="fb-logo" src="<?= $logo_url ?>" alt="logo" /></a>
        <div class="fb-nav-links-wrapper-mini">
            <div class="fb-nav-content-container">
                <?php foreach ($menu_items as $item): ?>
                    <div class="fb-nav-content">
                        <?php if (isset($item['id'])): ?>
                            <a id="<?= htmlspecialchars($item['id']) ?>" class="fb-link fb-nav-link"><?= htmlspecialchars($item['name']) ?></a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($item['url']) ?>" class="fb-link fb-nav-link"><?= htmlspecialchars($item['name']) ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="hamburger-icon" onclick="toggleMobileMenu()">
            <div class="line line1"></div>
            <div class="line line2"></div>
            <div class="line line3"></div>
        </div>
    </div>
    <div class="hamburger-menu"></div>
</nav>

<div id="fb-subcontentContainer" class="fb-nav-subcontent-container">
    <div id="fb-categoryContentWrapper" class="fb-nav-subcontent-wrapper">
        <?php foreach ($category_content as $content): ?>
            <a href="<?= htmlspecialchars($content["url"]) ?>">
                <div class="fb-nav-subcontent-frame">
                    <div class="fb-nav-subcontent-imgblock">
                        <img src="<?= htmlspecialchars($content['img']) ?>" alt="<?= htmlspecialchars($content['title']) ?> image" />
                        <h2 class="fb-subcontent-imgblock-title __t02-men1"><?= htmlspecialchars($content['title']) ?></h2>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
