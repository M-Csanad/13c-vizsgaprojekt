<?php
include_once __DIR__ . "/../init.php";
include_once __DIR__ . "/fuzzySearch.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = trim($_GET["q"] ?? "");

    // Ha üres a query, hibával térünk vissza
    if ($query === "") {
        http_response_code(400);
        echo (new Result(Result::ERROR, "Üres keresés."))->toJSON();
        exit;
    }

    // Lekérjük az összes termék adatait az adatbázisból és a hozzá tartozó értékeléseket
    $sql = "SELECT
        product.*,
        product_page.link_slug,
        MAX(CASE
            WHEN image.uri LIKE '%thumbnail%' THEN REGEXP_REPLACE(image.uri, '\\.[^.]*$', '')
        END) AS thumbnail_image,
        MAX(CASE
            WHEN image.uri LIKE '%vertical%' THEN REGEXP_REPLACE(image.uri, '\\.[^.]*$', '')
            WHEN image.uri NOT LIKE '%vertical%' AND image.uri NOT LIKE '%thumbnail%'
            THEN REGEXP_REPLACE(image.uri, '\\.[^.]*$', '')
        END) AS secondary_image,
        COALESCE(AVG(review.rating), 0) as avg_rating,
        COUNT(DISTINCT review.id) as review_count,
        GROUP_CONCAT(DISTINCT CONCAT(tag.id, ':', tag.name)) as tags
    FROM product_page
    INNER JOIN product ON product_page.product_id = product.id
    LEFT JOIN product_image ON product.id = product_image.product_id
    LEFT JOIN image ON product_image.image_id = image.id
    LEFT JOIN review ON product.id = review.product_id
    LEFT JOIN product_tag ON product.id = product_tag.product_id
    LEFT JOIN tag ON product_tag.tag_id = tag.id
    GROUP BY product.id
    ORDER BY product.name ASC";

    $result = selectData($sql, [], "");
    if ($result->isError()) {
        echo $result->toJSON(true);
        exit;
    }
    if ($result->isEmpty()) {
        echo (new Result(Result::ERROR, "Nincs termék az adatbázisban."))->toJSON();
        exit;
    }

    $rows = $result->message;
    $productMapping = [];
    foreach ($rows as $row) {
        $productMapping[$row['name']] = $row;
    }
    $names = array_keys($productMapping);

    // Fuzzy keresés a termékneveken
    $searchEngine = new LinearFuzzySearch($names);
    $fuzzyResultsRaw = $searchEngine->search($query);

    // A fuzzy eredményekhez csatoljuk a termék teljes adatait
    $results = [];
    foreach ($fuzzyResultsRaw as $item) {
        if (isset($productMapping[$item['word']])) {
            $results[] = $productMapping[$item['word']];
        }
    }

    echo (new Result(Result::SUCCESS, $results))->toJSON();
    exit;
}

http_response_code(405);
echo (new Result(Result::ERROR, "Hibás metódus"))->toJSON();
