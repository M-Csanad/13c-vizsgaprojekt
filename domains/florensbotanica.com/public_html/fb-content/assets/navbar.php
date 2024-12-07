<?php
include_once __DIR__ . '../../../../../.ext/db_connect.php';



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
    return trim($string, '-'); // Végéről kötőjelek eltávolítása
}

function getCategoryContent()
{
    $conn = createConnection();

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $sql = "SELECT
                category.name AS category_name,
                category.thumbnail_image_vertical_uri AS category_image,
                subcategory.name AS subcategory_name,
                subcategory.category_id AS category_id
            FROM category
            LEFT JOIN subcategory ON category.id = subcategory.category_id";

    $result = $conn->query($sql);
    $category_content = [];

    if ($result->num_rows > 0) {
        // Az adatok feldolgozása
        while ($row = $result->fetch_assoc()) {
            $category_name = $row['category_name'];
            $subcategory_name = $row['subcategory_name'];
            $category_image = $row['category_image'];

            // Kategória URL generálása
            $category_slug = generateSlug($category_name);
            $category_url = "http://localhost:8080/13c-vizsgaprojekt/src/web/" . $category_slug . "/";

            if (!isset($category_content[$category_name])) {
                $category_content[$category_name] = [
                    'title' => $category_name,
                    'url' => $category_url,
                    'img' => "../" . $category_image,
                    'subcategories' => []
                ];
            }






            if ($subcategory_name) {
                // Alkategória URL generálása
                $subcategory_slug = generateSlug($subcategory_name);
                $subcategory_url = "http://localhost:8080/13c-vizsgaprojekt/src/web/" . $category_slug . "/" . $subcategory_slug;

                $category_content[$category_name]['subcategories'][] = [
                    'name' => $subcategory_name,
                    'url' => $subcategory_url
                ];
            }
        }
    }

    $conn->close();

    return array_values($category_content); // Az asszociatív indexeket numerikusra váltja
}
$category_content = getCategoryContent();
$base_url = "http://localhost:8080/13c-vizsgaprojekt/src/web";
$logo_url = "$base_url/media/img/herbalLogo_mini_white.png";
// Navigációs elemek
$menu_items = [
    ['name' => 'Categories', 'id' => 'fb-navlink-category', 'url' => '#'],
    ['name' => 'About us', 'url' => "$base_url/about-us/"],
    ['name' => 'Privacy Policy', 'url' => "$base_url/privacy-policy/"]
];
?>


<nav id="fb-navbar" class="fb-sticky" data-category='<?= json_encode($category_content); ?>'>
    <div id="fb-navTopWrapper" class="fb-nav-links-wrapper">
        <a href="<?= $base_url ?>"><img class="fb-logo" src="<?= $logo_url ?>" alt="logo" /></a>
        <div class="fb-nav-links-wrapper-mini">
            <div class="fb-nav-content-container">
                <?php foreach ($menu_items as $item): ?>
                    <div class="fb-nav-content">
                        <?php if (isset($item['id'])): ?>
                            <a id="<?= $item['id'] ?>" class="fb-link fb-nav-link"><?= $item['name'] ?></a>
                        <?php else: ?>
                            <a href="<?= $item['url'] ?>" class="fb-link fb-nav-link"><?= $item['name'] ?></a>
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
            <a href="<?= $content["url"] ?>">
                <div class="fb-nav-subcontent-frame">
                    <div class="fb-nav-subcontent-imgblock">
                        <img src="<?= $content['img'] ?>" alt="<?= $content['title'] ?> image" />
                        <h2 class="fb-subcontent-imgblock-title"><?= $content['title'] ?></h2>
                    </div>
                    <!-- <div class="fb-nav-subcontent-textblock">
                        <h4 class="fb-textblock-title"><?= $content['title'] ?></h4>
                        <div class="fb-textblock-listpanel">
                            <ul>
                                <?php foreach ($content['subcategories'] as $sub): ?>
                                    <li><a href="<?= $sub['url'] ?>" class="fb-link"><?= $sub['name'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div> -->
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
