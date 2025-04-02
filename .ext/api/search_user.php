<?php

$searchTerm = $_POST['search_term'] ?? '';
$wildCard = "%".$searchTerm."%";

if ($searchTerm) {
    include_once __DIR__."/../init.php";
    
    $matches = null;

    if (is_numeric($searchTerm)) {
        $searchTerm = intval($searchTerm);
        $matches = selectData("SELECT user.id, user.user_name as 'name', user.role, user.email FROM user WHERE user.id = ?;", [$searchTerm], "i");
    }
    else {
        $matches = selectData("SELECT user.id, user.user_name as 'name', user.role, user.email
                               FROM user 
                               WHERE user.user_name LIKE ? 
                               OR user.email LIKE ?
                               OR user.id = ?
                               ORDER BY user.role, user.user_name;", 
                               [$wildCard, $wildCard, $searchTerm], "sss");
    }

    echo $matches->toJSON();
}