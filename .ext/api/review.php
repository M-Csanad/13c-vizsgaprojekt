<?php
include_once __DIR__.'/../review_functions.php';
include_once __DIR__.'/../result_functions.php';

// GET kérés kezelése vélemények lekéréséhez lapozással
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['product_id'])) {
        http_response_code(400);
        echo (new Result(Result::ERROR, "Hiányzó termék azonosító"))->toJSON();
        exit();
    }

    $productId = intval($_GET['product_id']);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 5;

    $result = getProductReviews($productId, $page, $perPage);

    if ($result->isError()) {
        http_response_code(400);
    }

    echo $result->toJSON();
    exit();
}

// PUT kérés kezelése új vélemény beküldéséhez
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

$fields = ["review-title", "review-body", "rating"];
$values = [];

$body = json_decode(file_get_contents("php://input"), true);

foreach ($fields as $field) {
    if (!isset($body[$field]) || empty($body[$field])) {
        http_response_code(400);
        echo (new Result(Result::ERROR, "Hiányos adat: ".$field))->toJSON();
        exit();
    }

    $values[$field] = $body[$field];
}
$values["HTTP_REFERER"] = $_SERVER["HTTP_REFERER"];

$result = makeReview($values);
if (!$result->isSuccess()) {
    http_response_code(400);
    echo $result->toJSON();
    exit();
}

echo (new Result(Result::SUCCESS, "Sikeres értékelés!"))->toJSON();
