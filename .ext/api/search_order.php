<?php

$searchTerm = $_POST['search_term'] ?? '';

if ($searchTerm) {
    include_once __DIR__."/../init.php";
    $matches = selectData("SELECT `order`.id, `order`.created_at, `order`.status, `order`.order_total, 
                                CONCAT(order.last_name, ' ', order.first_name) as user_name, order.email as user_email
                           FROM `order`
                           LEFT JOIN user ON `order`.user_id = user.id
                           WHERE `order`.id=?",
                           $searchTerm, "s");

    echo $matches->toJSON();
}