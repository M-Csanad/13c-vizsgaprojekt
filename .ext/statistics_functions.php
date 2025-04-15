<?php

include_once "init.php";
function getStatistics() {
    $statistics = [];
    $data = [];

    $data['total_users'] = getTotalUsers();
    $data['total_orders'] = getTotalOrders();
    $data['total_products'] = getTotalProducts();
    $data['total_reviews'] = getTotalReviews();
    $data['total_categories'] = getTotalCategories();
    $data['total_subcategories'] = getTotalSubcategories();
    $data['total_orders_value'] = getTotalOrdersValue();
    

    foreach ($data as $key => $value) {
        if ($value->isSuccess()) {
            if ($key == 'total_orders_value') {
                $statistics[$key] = $value->message[0];
            } else {
                $statistics[$key] = $value->message[0]["total"];
            }
        } else {
            log_Error("Hiba merült fel a(z) $key lekérdezésekor: " . $value->toJSON(true), 'statistics_error.log');
            return $value;
        }
    }

    return $statistics;
}