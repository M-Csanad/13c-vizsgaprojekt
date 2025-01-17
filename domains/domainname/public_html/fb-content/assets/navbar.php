<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . "/../../../.ext/init.php";
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
            $category_url = "/" . $category_slug . "/";

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
if ($isLoggedIn) {
    $pfpURL = $user["pfp_uri"];
}
// Navigációs elemek
$base_url = "http://localhost";
$logo_url = htmlspecialchars("$base_url/fb-content/assets/media/images/logos/herbalLogo_mini_white.png");
$menu_items = [
    ['name' => 'Kategóriák', 'id' => 'fb-navlink-category', 'url' => '#'],
    ['name' => 'Rólunk', 'url' => "$base_url/about-us/"],
    ['name' => 'Adatvédelmi Nyilatkozat', 'url' => "$base_url/privacy-policy"]
];



?>


<!--root stylesheet-->
<link rel="stylesheet" href="/fb-content/assets/css/root.css" />

<!--casual stylesheets-->
<link rel="stylesheet" href="/fb-content/assets/css/box.css" />
<link rel="stylesheet" href="/fb-content/assets/css/font.css" />
<link rel="stylesheet" href="/fb-content/assets/css/navbar.css" media="all" />
<link rel="stylesheet" href="/fb-content/assets/css/mobileNavbar.css" media="all">
<link rel="stylesheet" href="/fb-content/assets/css/cart.css">
<link rel="stylesheet" href="/fb-content/assets/css/search.css">
<link rel="stylesheet" href="/fb-content/assets/css/popup.css">
<link rel="stylesheet" href="/fb-content/fb-products/css/numberfield.css">

<script type="module" src="/fb-content/assets/js/popup.js"></script>
<script src="/fb-content/fb-products/js/numberfield.js"></script>
<script type="module" src="/fb-content/assets/js/cart.js"></script>
<script type="module">
    import Cart from "/fb-content/assets/js/cart.js";
    const cart = new Cart();
</script>
<script defer type="module" src="/fb-content/assets/js/search.js"></script>
<script defer type="module" src="/fb-content/assets/js/autogenerate__navbar.js"></script>

<nav id="fb-navbar" class="fb-sticky" data-category='<?= json_encode($category_content); ?>'>
    <div id="fb-navTopWrapper" class="fb-nav-links-wrapper">
        <a id="logo_linkNav" href="<?= htmlspecialchars($base_url) ?>"><img class="fb-logo" src="<?= $logo_url ?>"
                alt="logo" /></a>
        <div class="fb-nav-links-wrapper-mini">
            <div class="fb-nav-content-container">
                <?php foreach ($menu_items as $item): ?>
                    <div class="fb-nav-content">
                        <?php if (isset($item['id'])): ?>
                            <a id="<?= htmlspecialchars($item['id']) ?>"
                                class="fb-link fb-nav-link"><?= htmlspecialchars($item['name']) ?></a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($item['url']) ?>"
                                class="fb-link fb-nav-link"><?= htmlspecialchars($item['name']) ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="hamburger-icon">
            <div class="line line1"></div>
            <div class="line line2"></div>
            <div class="line line3"></div>
        </div>
        <div id="interfaceIcons">
            <div class="icon_container">
                <div class="icon_wrapper">
                    <a title="Keresés" class="search-open">
                        <svg width="40px" height="40px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.7955 15.8111L21 21M18 10.5C18 14.6421 14.6421 18 10.5 18C6.35786 18 3 14.6421 3 10.5C3 6.35786 6.35786 3 10.5 3C14.6421 3 18 6.35786 18 10.5Z"
                                stroke="#ffffff" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
                <div class="icon_wrapper">
                    <a title="Kosár" class="cart-open">
                        <svg width="34px" height="34px" viewBox="0 0 1024 1063.18" xmlns="http://www.w3.org/2000/svg"><path fill="white" d="M93.09 288.038h846.197V888.94c-.106 86.33-70.062 156.287-156.383 156.393h-533.42C163.15 1045.227 93.195 975.27 93.09 888.95v-.01zm799.652 46.546H139.637V888.94c.08 60.635 49.212 109.768 109.84 109.847h533.42c60.634-.08 109.767-49.212 109.846-109.84v-.008zM761.484 444.43H714.94V266.628c0-109.766-88.984-198.75-198.75-198.75s-198.75 88.984-198.75 198.75V444.43h-46.544V266.628c0-135.472 109.822-245.295 245.295-245.295s245.296 109.822 245.296 245.295z"/></svg>
                    </a>
                </div>
                <div class="icon_wrapper login_icon_wrapper">
                    <?php if ($isLoggedIn): ?>
                        <!-- Profil ikon jelenik meg, ha be van jelentkezve -->
                        <a id="profileLink_icon" href="/settings" title="Profil">
                            <div class="profile-pic">
                                <img src="<?= htmlspecialchars($pfpURL); ?>" alt="Profilkép" />
                            </div>
                        </a>

                    <?php else: ?>
                        <!-- Login ikon jelenik meg, ha nincs bejelentkezve -->
                        <a href="/login" title="Login">
                            <svg width="40px" height="40px" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="9" r="3.5" stroke="#fff" stroke-width="0.8" />
                                <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="0.8" />
                                <path
                                    d="M17.9691 20C17.81 17.1085 16.9247 15 11.9999 15C7.07521 15 6.18991 17.1085 6.03076 20"
                                    stroke="#fff" stroke-width="0.8" stroke-linecap="round" />
                            </svg>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <div class="hamburger-menu hidden">
        <?php include 'mobileNavbar.php'; ?>
    </div>
</nav>


<div id="fb-subcontentContainer" class="fb-nav-subcontent-container">
    <div id="fb-categoryContentWrapper" class="fb-nav-subcontent-wrapper">
        <?php foreach ($category_content as $content): ?>
            <a href="<?= htmlspecialchars($content["url"]) ?>">
                <div class="fb-nav-subcontent-frame">
                    <div class="fb-nav-subcontent-imgblock">
                        <img src="<?= htmlspecialchars($content['img']) ?>"
                            alt="<?= htmlspecialchars($content['title']) ?> image" />
                        <h2 class="fb-subcontent-imgblock-title __t02-men1"><?= htmlspecialchars($content['title']) ?></h2>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal-background"></div>
<div class="search">
    <div class="search-input">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search search-button" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
        </svg>
        <input type="text" name="search_term" placeholder="Keresés">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg search-close" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
        </svg>
    </div>
</div>

<div class="cart">
    <div class="cart-close">
        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
        </svg>
    </div>
    <svg class="cart-logo" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g stroke="white" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m4.05108 8.9199c.04175-.51955.47556-.9199.99679-.9199h13.90423c.5213 0 .9551.40035.9968.9199l.8775 10.9199c.0935 1.164-.8258 2.1602-1.9936 2.1602h-13.66564c-1.16773 0-2.08711-.9962-1.99357-2.1602z"/><path d="m16 11v-5c0-2.20914-1.7909-4-4-4-2.20914 0-4 1.79086-4 4v5"/></g></svg>
    <header>
        <div class="title">Kosár</div>
        <div class="cart-count">0 elem</div>
    </header>
    <div class="cart-main" data-lenis-prevent>
        <div class="cart-empty">
            <span>Még nincsenek termékek a kosárban.</span>
        </div>
        <div class="cart-items"></div>
    </div>
    <div class="cart-bottom-button">
        <a href="/checkout">
            <div class="text">
                FIZETÉS
            </div>
            <div class="text-separator">
                -
            </div>
            <div class="price">
                <div class="value">8000</div>    
                <div class="currency">Ft</div>
            </div>
        </a>
    </div>
</div>

<script src="http://localhost/fb-content/assets/js/mobileNavbar.js"></script>
