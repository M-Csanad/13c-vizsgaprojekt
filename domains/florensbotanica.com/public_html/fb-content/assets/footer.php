<?php
$base_url = "https://florensbotanica.com/fb-content/assets/media/images/logos/herbalLogo_white.png";
$footerData = [
    'logo' => "$base_url/media/img/herbalLogo_white.png",
    'description' => 'We bring the healing power of nature into modern wellness, offering pure, Ayurveda-inspired products for balanced health and well-being.',
    'sections' => [
        'Website' => [
            ['text' => 'Home', 'url' => "$base_url"],
            ['text' => 'Categories', 'url' => "$base_url#categoryGallery"],
            ['text' => 'Privacy Policy', 'url' => '#'],
            ['text' => 'About us', 'url' => '#'],
            ['text' => 'Copyright', 'url' => '#'],
        ],
        'Our Team' => [
            ['name' => 'Csanad', 'url' => '#', 'icon' => 'construct-outline'],
            ['name' => 'Máté', 'url' => '#', 'icon' => 'construct-outline'],
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

