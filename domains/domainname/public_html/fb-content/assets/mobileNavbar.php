<?php

include_once __DIR__ . '/../../config.php';
$loggerPath = BASE_PATH . '/error_logger.php';
$filePath = BASE_PATH . '/../../../.ext/db_connect.php';



function fetchCategories($conn) {
    $sql = "SELECT c.id, c.name AS category_name, s.name AS subcategory_name
            FROM category c
            LEFT JOIN subcategory s ON c.id = s.category_id";

    $result = $conn->query($sql);

    if (!$result) {
        die("Database query failed: " . $conn->error);
    }

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categoryId = $row['id'];
        if (!isset($categories[$categoryId])) {
            $categories[$categoryId] = [
                'name' => $row['category_name'],
                'subcategories' => []
            ];
        }

        if ($row['subcategory_name']) {
            $categories[$categoryId]['subcategories'][] = $row['subcategory_name'];
        }
    }

    return $categories;
}

$conn = db_connect();
$categories = fetchCategories($conn);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Navbar</title>
    <link rel="stylesheet" href="http://localhost/fb-content/assets/css/mobileNavbar.css">
</head>
    <div class="navbar">
        <ul id="main-menu" class="menu">
            <li class="menu-item" data-target="shop-all">Shop All</li>
            <li class="menu-item"><a href="about-us.php">About Us</a></li>
            <li class="menu-item"><a href="privacy-policy.php">Privacy Policy</a></li>
        </ul>

        <div id="shop-all" class="submenu">
            <button class="back" data-back>Back</button>
            <ul class="menu">
                <?php foreach ($categories as $category): ?>
                    <li class="menu-item">
                        <?= htmlspecialchars($category['name']) ?>
                        <?php if (!empty($category['subcategories'])): ?>
                            <ul class="submenu">
                                <?php foreach ($category['subcategories'] as $subcategory): ?>
                                    <li class="menu-item">- <?= htmlspecialchars($subcategory) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script src="http://localhost/fb-content/assets/js/mobileNavbar.js"></script>

