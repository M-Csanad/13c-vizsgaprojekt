<?php

$searchTerm = $_POST['search_term'] ?? '';
$searchTerm = "%".$searchTerm."%";

if ($searchTerm) {
    include_once __DIR__."/../init.php";
    
    $matches = selectData("SELECT user.id, user.user_name as 'name', user.role, user.email
                           FROM user 
                           WHERE user.user_name LIKE ? 
                           OR user.email LIKE ?
                           ORDER BY user.role, user.user_name;", 
                           array_fill(0, 2, $searchTerm), "ss");

    echo $matches->toJSON();
}