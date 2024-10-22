<?php

    function getUserData($userId) {
        include "./auth/db_connect.php";
        
        try {
            $userStatement = $db -> prepare("SELECT user.email, user.user_name, user.role, user.first_name, user.last_name, user.created_at, delivery_info.* FROM user LEFT JOIN delivery_info ON delivery_info.user_id = user.id WHERE user.id = ?");
            $userStatement -> bind_param("i", $userId);
            $userStatement -> execute();
            $result = $userStatement -> get_result();
    
            if ($result -> num_rows < 1) {
                return null;
            }
    
            $data = $result -> fetch_assoc();
    
            return $data;
        }
        catch (Exception $error) {
            return $error;
        }
    }

    function modifyUserData($userId, $userData) {
        include "./auth/db_connect.php";

        var_dump($userData);
    }

?>