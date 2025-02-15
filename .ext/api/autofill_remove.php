<?php
include_once __DIR__.'/../autofill_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: DELETE');
    echo $result->toJSON();
    exit();
}

if (!isset($_REQUEST['type']) || $_REQUEST['type'] !== 'delivery' && $_REQUEST['type'] !== "billing" && $_REQUEST['type'] !== "all") {
    http_response_code(400);
    $result = new Result(Result::ERROR, "Hiányos vagy hibás típus.");
    echo $result->toJSON();
    exit();
}
$type = $_REQUEST['type'];

if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    http_response_code(400);
    $result = new Result(Result::ERROR, "Hiányos vagy hibás azonosító.");
    echo $result->toJSON();
    exit();
}

$id = intval($_REQUEST['id']);
$result = deleteAutofill($id, $type);
if ($result->isError()) {
    return $result->messageJSON();
}