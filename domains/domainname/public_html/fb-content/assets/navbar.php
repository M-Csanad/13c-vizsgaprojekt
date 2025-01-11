<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . "/../../../.ext/init.php";
$loggerPath = BASE_PATH . '/error_logger.php';
$filePath = BASE_PATH . '/../../../.ext/db_connect.php';


// Felhasználó adatainak lekérdezése
$isLoggedIn = false;
$result = getUserData();

if ($result->isSuccess()) {
    $user = $result->message;
    $isLoggedIn = true;
}

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
    $user = $result->message[0];
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

<script defer type="module" src="/fb-content/assets/js/cart.js"></script>
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
                    <a title="Keresés">
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
                        <svg width="40px" height="40px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z"
                                stroke="#ffffff" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
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

<div class="cart-background"></div>
<div class="cart">
    <div class="cart-close">
        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
        </svg>
    </div>
    <header>
        <div class="title">Kosár</div>
        <div class="cart-count">3 elem</div>
    </header>
    <div class="cart-main" data-lenis-prevent>
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Laboriosam quasi omnis eveniet animi molestias corporis sit saepe provident, quaerat eos eius veritatis quia? Eveniet aut, quod voluptatum minus libero saepe.
    </div>
    <div class="cart-bottom-button">
        <button>
            <div class="text">
                TOVÁBB A FIZETÉSHEZ
            </div>
            <div class="text-separator">
                -
            </div>
            <div class="price">
                8000 FT
            </div>
        </button>
    </div>
</div>


<script src="http://localhost/fb-content/assets/js/mobileNavbar.js"></script>
