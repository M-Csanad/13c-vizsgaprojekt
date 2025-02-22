<?php

include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: PUT');
    echo $result->toJSON();
    exit();
}

echo json_encode("Reset password endpoint", JSON_UNESCAPED_UNICODE);
exit();