<?php
include_once __DIR__ . "/../init.php";
include_once __DIR__ . "/fuzzySearch.php"; // Itt vannak a damerauLevenshtein(), partialSubstrDistance(), LinearFuzzySearch, stb.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $query = trim($_POST["query"] ?? "");

    // Ha üres a query, hibával térünk vissza
    if ($query === "") {
        $_SESSION['search_error'] = "Üres keresés.";
        exit;
    }

    // Lekérjük az összes termék adatait az adatbázisból
    $sql = "SELECT * FROM product";
    $result = selectData($sql, [], "");
    if ($result->isError()) {
        $_SESSION['search_error'] = "Adatbázis hiba: " . $result->message;
        exit;
    }
    if ($result->isEmpty()) {
        $_SESSION['search_info'] = "Nincs termék az adatbázisban.";
        exit;
    }

    $rows = $result->message;
    // Építünk egy termékmappát: termék neve => teljes adatok
    $productMapping = [];
    foreach ($rows as $row) {
        $productMapping[$row['name']] = $row;
    }
    $names = array_keys($productMapping);

    // Fuzzy keresés a termékneveken
    $searchEngine = new LinearFuzzySearch($names);
    $fuzzyResultsRaw = $searchEngine->search($query);

    // A fuzzy eredményekhez csatoljuk a termék teljes adatait
    $fuzzyResults = [];
    foreach ($fuzzyResultsRaw as $item) {
        if (isset($productMapping[$item['word']])) {
            $item['product'] = $productMapping[$item['word']];
            $fuzzyResults[] = $item;
        }
    }

    // Tároljuk a keresési kifejezést és a találatokat a session-ben

    $_SESSION['search_query'] = $query;
    $_SESSION['search_results'] = $fuzzyResults;
    var_dump(session_id());

    // Átirányítás a search_result.php oldalra
    header("Location: /search");
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_UNICODE);
