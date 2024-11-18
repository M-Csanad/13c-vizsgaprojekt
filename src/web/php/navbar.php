<?php
$base_url = "http://localhost:8080/13c-vizsgaprojekt/src/web"; //Ez olyan, mintha a domain cím lenne (public_html).
$logo_url = "$base_url/media/img/herbalLogo_mini_white.png";

// Navigációs elemek
$menu_items = [
    ['name' => 'Categories', 'id' => 'fb-navlink-category', 'url' => '#'],
    ['name' => 'About us', 'url' => "$base_url/about-us/"],
    ['name' => 'Privacy Policy', 'url' => "$base_url/privacy-policy/"]
];

// Kategória tartalom
$category_content = [
    [
        'title' => 'Travel',
        'img' => "https://images.unsplash.com/photo-1511207538754-e8555f2bc187?q=80&w=2412&auto=format&fit=crop",
        'subcategories' => [
            ['name' => 'backpack', 'url' => "$base_url/C:/category-travel/subcategory-backpack/"],
            ['name' => 'tent', 'url' => "$base_url/C:/category-travel/subcategory-tent/"]
        ]
    ],
    [
        'title' => 'Perfume',
        'img' => "https://images.unsplash.com/photo-1533603208986-24fd819e718a?q=80&w=3774&auto=format&fit=crop",
        'subcategories' => [
            ['name' => 'pheromone', 'url' => "$base_url/C:/category-perfume/subcategory-pheromone/"],
            ['name' => 'edp', 'url' => "$base_url/C:/category-perfume/subcategory-edp/"]
        ]
    ]
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
    <div id="fb-categoryContentWrapper" class="col-8 fb-nav-subcontent-wrapper">
        <?php foreach ($category_content as $content): ?>
            <div class="fb-nav-subcontent-frame">
                <div class="fb-nav-subcontent-imgblock">
                    <img src="<?= $content['img'] ?>" alt="<?= $content['title'] ?> image" />
                    <h3 class="fb-subcontent-imgblock-title"><?= $content['title'] ?></h3>
                </div>
                <div class="fb-nav-subcontent-textblock">
                    <h2 class="fb-textblock-title"><?= $content['title'] ?></h2>
                    <div class="fb-textblock-listpanel">
                        <ul>
                            <?php foreach ($content['subcategories'] as $sub): ?>
                                <li><a href="<?= $sub['url'] ?>" class="fb-link"><?= $sub['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
