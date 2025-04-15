<?php

$searchTerm = $_POST['search_term'] ?? '';

if ($searchTerm) {
    include_once __DIR__ . "/../init.php";

    $wildcardTerm = "%$searchTerm%";

    $matches = selectData("
        SELECT 
            `order`.id, 
            `order`.created_at, 
            `order`.status, 
            `order`.order_total, 
            CONCAT(user.last_name, ' ', user.first_name) as user_name, 
            `order`.email as user_email,
            `order`.phone as user_phone,
            `order`.delivery_address,
            `order`.billing_address,
            `order`.company_name,
            `order`.tax_number,
            GROUP_CONCAT(CONCAT(product.name, ':', CONCAT(order_item.quantity, ',', product.net_weight)) SEPARATOR ';') AS order_items
        FROM `order`
        LEFT JOIN user ON `order`.user_id = user.id
        LEFT JOIN order_item ON `order`.id = order_item.order_id
        LEFT JOIN product ON order_item.product_id = product.id
        WHERE 
            `order`.status != 'Törölve' AND (
                `order`.id LIKE ? OR 
                user.first_name LIKE ? OR 
                user.last_name LIKE ? OR 
                CONCAT(user.last_name, ' ', user.first_name) LIKE ? OR 
                user.email LIKE ? OR
                user.phone LIKE ?
            ) 
        GROUP BY `order`.id
        LIMIT 100;
    ", 
    array_fill(0, 6, $wildcardTerm), "ssssss");

    echo $matches->toJSON();
}
