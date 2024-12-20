<?php

include_once __DIR__ . '/../../config.php';
$loggerPath = BASE_PATH . '/error_logger.php';
$filePath = BASE_PATH . '/../../../.ext/db_connect.php';



function fetchCategories($conn)
{
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
                'id' => $categoryId,
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
<div class="navbar">
    <ul id="main-menu" class="menu">
        <li class="menu-item __t01-mew2" data-target="shop-all">
            <p class="__t01-mew2">Minden termék</p> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"
                width="25" height="25" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink">
                <path style="fill:#fff"
                    d="m21 7.199 -0.848 0.848 6.352 6.352H1.2v1.2h25.303l-6.353 6.354 0.848 0.848L28.8 14.999z"
                    data-name="Right"></path>
            </svg>
        </li>
        <li class="menu-item __t01-mew2" data-target="top-products">
            <p class="__t01-mew2">Legjobb termékek</p> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"
                width="25" height="25" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink">
                <path style="fill:#fff"
                    d="m21 7.199 -0.848 0.848 6.352 6.352H1.2v1.2h25.303l-6.353 6.354 0.848 0.848L28.8 14.999z"
                    data-name="Right"></path>
            </svg>
        </li>
        <li class="menu-item __t01-mew2"><a href="about-us.php">Rólunk</a></li>
        <li class="menu-item __t01-mew2"><a href="privacy-policy.php">Jogi nyilatkozat</a></li>

    </ul>

    <div id="shop-all" class="submenu">
        <button class="back" data-back>
            <div class="back-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" transform="scale(-1 1)" width="25"
                    height="25">
                    <path style="fill:#fff"
                        d="m21 7.199 -0.848 0.848 6.352 6.352H1.2v1.2h25.303l-6.353 6.354 0.848 0.848L28.8 14.999z"
                        data-name="Right" />
                </svg>
            </div>
            <div class="back-text">
                <p class="__t01-mew2">Vissza</p>
            </div>
        </button>
        <ul class="menu">
            <?php foreach ($categories as $category): ?>
                <li class="menu-item __t01-mew2" data-target="category-<?= htmlspecialchars($category['id']) ?>">
                    <p class="__t01-mew2">
                        <a
                            href="http://localhost/<?= htmlspecialchars(strtolower(str_replace(' ', '-', strtr($category['name'], ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ö' => 'o', 'ő' => 'o', 'ú' => 'u', 'ü' => 'u', 'ű' => 'u', 'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ö' => 'O', 'Ő' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ű' => 'U'])))) ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    </p>
                    <?php if (!empty($category['subcategories'])): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="25" height="25" version="1.1">
                            <path style="fill:#fff"
                                d="m21 7.199 -0.848 0.848 6.352 6.352H1.2v1.2h25.303l-6.353 6.354 0.848 0.848L28.8 14.999z"
                                data-name="Right"></path>
                        </svg>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php foreach ($categories as $category): ?>
        <?php if (!empty($category['subcategories'])): ?>
            <div id="category-<?= htmlspecialchars($category['id']) ?>" class="submenu">
                <button class="back" data-back>
                    <div class="back-logo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" transform="scale(-1 1)" width="25"
                            height="25">
                            <path style="fill:#fff"
                                d="m21 7.199 -0.848 0.848 6.352 6.352H1.2v1.2h25.303l-6.353 6.354 0.848 0.848L28.8 14.999z"
                                data-name="Right" />
                        </svg>
                    </div>
                    <div class="back-text">
                        <p class="__t01-mew2">Vissza a kategóriákhoz</p>
                    </div>
                </button>
                <ul class="menu">
                    <?php foreach ($category['subcategories'] as $subcategory): ?>
                        <li class="menu-item __t01-mew2">
                            <p class="__t01-mew2"><a
                                    href="http://localhost/<?= htmlspecialchars(strtolower(str_replace(' ', '-', strtr($category['name'], ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ö' => 'o', 'ő' => 'o', 'ú' => 'u', 'ü' => 'u', 'ű' => 'u', 'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ö' => 'O', 'Ő' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ű' => 'U'])))) ?>/<?= htmlspecialchars(strtolower(str_replace(' ', '-', strtr($subcategory, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ö' => 'o', 'ő' => 'o', 'ú' => 'u', 'ü' => 'u', 'ű' => 'u', 'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ö' => 'O', 'Ő' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ű' => 'U'])))) ?>"><?= htmlspecialchars($subcategory) ?></a>
                            </p>

                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

</div>
