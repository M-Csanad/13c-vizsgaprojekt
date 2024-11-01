<?php
$footerData = [
    'logo' => '	http://localhost:8080/FlorensBotanica/GIT_REPO/src/web/media/img/herbalLogo_white.png',
    'description' => 'We bring the healing power of nature into modern wellness, offering pure, Ayurveda-inspired products for balanced health and well-being.',
    'sections' => [
        'Website' => [
            ['text' => 'Home', 'url' => 'http://localhost:8080/FlorensBotanica/GIT_REPO/src/web/mainPage/main.html'],
            ['text' => 'Categories', 'url' => 'http://localhost:8080/FlorensBotanica/GIT_REPO/src/web/mainPage/main.html#categoryGallery'],
            ['text' => 'Privacy Policy', 'url' => '#'],
            ['text' => 'About us', 'url' => '#'],
            ['text' => 'Copyright', 'url' => '#'],
        ],
        /* 'Category' => [
            ['text' => 'Travel', 'url' => 'https://blogmarket.shop/homepage/category-travel'],
            ['text' => 'Nippon', 'url' => 'https://blogmarket.shop/homepage/category-japan'],
            ['text' => 'Perfume', 'url' => 'https://blogmarket.shop/homepage/category-perfume'],
        ], */
        'Our Team' => [
            ['name' => 'ScalyShop', 'url' => 'https://www.instagram.com/scaly_san/', 'icon' => 'logo-instagram'],
            ['name' => 'Migu', 'url' => '#', 'icon' => 'text-outline'],
            ['name' => 'Csanad', 'url' => '#', 'icon' => 'construct-outline'],
        ],
        'Sitemap' => [
            ['text' => 'Search & Sitemap', 'url' => '#'],
            ['text' => 'Sitemap_pages', 'url' => '#'],
        ],
    ],
    'copyright' => 'Copyright © 2024-2025 Florens Botanica, Sole Proprietor. The brand name "Florens Botanica" and company logo of the website and all design and text content are also individually created. All rights reserved.'
];
header('Content-Type: application/json');
echo json_encode($footerData);
?>

