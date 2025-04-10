<?php
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: GET');
    echo $result->toJSON();
    exit();
}

include_once __DIR__."/../init.php";
$reviewsPerPage = 3;

if (!isset($_GET['url']) || !isset($_GET['page'])) {
    http_response_code(400);
    echo (new Result(Result::ERROR, "Hiányos adatok!"))->toJSON();
    exit();
}

$data = getProductIdFromURL($_GET['url']);
if ($data->isError()) {
    return $data->toJSON();
}
$id = $data->message['id'];
$page = $_GET['page'];

$result = getProductReviews($id, $page, $reviewsPerPage);
if ($result->isError()) {
    http_response_code(400);
}
echo $result->toJSON(true);
exit();