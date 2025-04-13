<?php

$searchTerm = $_POST['search_term'] ?? '';

if ($searchTerm) {
    include_once __DIR__."/../init.php";

    $wildcardTerm = "%$searchTerm%";

    $matches = selectData("
        SELECT 
            `order`.id, 
            `order`.created_at, 
            `order`.status, 
            `order`.order_total, 
            CONCAT(user.last_name, ' ', user.first_name) as user_name, 
            user.email as user_email,
            user.phone as user_phone
        FROM `order`
        LEFT JOIN user ON `order`.user_id = user.id
        WHERE 
            `order`.status != 'Törölve' AND (
                `order`.id LIKE ? OR 
                user.first_name LIKE ? OR 
                user.last_name LIKE ? OR 
                CONCAT(user.last_name, ' ', user.first_name) LIKE ? OR 
                user.email LIKE ? OR
                user.phone LIKE ?
            ) 
        LIMIT 100;
    ", 
    array_fill(0, 6, $wildcardTerm), "ssssss");

    echo $matches->toJSON();
}