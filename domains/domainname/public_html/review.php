<?php
include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/review.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/result_functions.php';

if ($_SERVER["REQUEST_METHOD"] != "PUT") {
    echo json_encode(["message" => "Hibás metódus! Várt: PUT, Aktuális: ".$_SERVER["REQUEST_METHOD"], "type" => "ERROR"], JSON_UNESCAPED_UNICODE);
    header("bad request", true, 400);
    return;
}

$fields = ["review-title", "review-body", "rating"];
$values = [];

$body = json_decode(file_get_contents("php://input"), true);

foreach ($fields as $field) {
    if (!isset($body[$field]) || empty($body[$field])) {
        header("bad request", true, 400);
        echo json_encode(["message" => "Hiányos adat: ".$field, "type" => "ERROR"], JSON_UNESCAPED_UNICODE);
        return;
    }

    $values[$field] = $body[$field];
}
$values["HTTP_REFERER"] = $_SERVER["HTTP_REFERER"];

$result = makeReview($values);
if (!$result->isSuccess()) {
    echo json_encode(["message" => "Hiba történt a feltöltés során: ".$result->toJSON(), "type" => "ERROR"], JSON_UNESCAPED_UNICODE);
    header("bad request", true, 400);
}

echo json_encode(["message" => "Sikeres értékelés!", "type" => "SUCCESS"], JSON_UNESCAPED_UNICODE);