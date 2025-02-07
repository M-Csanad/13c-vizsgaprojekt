<?php
include_once __DIR__.'/../review_functions.php';
include_once __DIR__.'/../result_functions.php';

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