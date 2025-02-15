<?php
include_once __DIR__."/../init.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $query = trim($_POST["query"] ?? "");

    
    $_SESSION['search_query'] = $query;
    $_SESSION['search_results'] = "ASD";

    exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed"], JSON_UNESCAPED_UNICODE);