<?php

$searchTerm = $_POST['search_term'] ?? '';
$wildCard = "%".$searchTerm."%";

if ($searchTerm) {
    include_once __DIR__."/../init.php";
    
    $matches = null;

    $includeSelf = false;

    if (is_numeric($searchTerm)) {
        $searchTerm = intval($searchTerm);

        $query = "SELECT user.id, user.user_name as 'name', user.role, user.email, user.first_name, user.last_name, user.phone FROM user WHERE user.id = ? LIMIT 100";
        $params = [$searchTerm];
        $typeString = "i";
        if (!$includeSelf) {
            $query .= " AND user.id <> ?";
            $params[] = $user["id"];
            $typeString .= "i";
        }
        $query .= ";";


        $matches = selectData($query, $params, $typeString);
    }
    else {
        $query = "SELECT user.id, user.user_name as 'name', user.role, user.email, user.first_name, user.last_name, user.phone
                    FROM user 
                    WHERE (user.user_name LIKE ? 
                    OR user.email LIKE ?
                    OR user.id = ?)";
        $params = [$wildCard, $wildCard, $searchTerm];
        $typeString = "sss";
        if (!$includeSelf) {
            $query .= " AND user.id <> ?";
            $params[] = $user["id"];
            $typeString .= "i";
        }
        $query .= " ORDER BY user.role, user.user_name LIMIT 100;";

        $matches = selectData($query, $params, $typeString);
    }

    echo $matches->toJSON();
}