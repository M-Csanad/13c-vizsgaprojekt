<?php

    function getUserData($userId) {
        include_once "./auth/init.php";
        $result = selectData("SELECT user.email, 
                                     user.user_name, 
                                     user.role, 
                                     user.first_name, 
                                     user.last_name, 
                                     user.created_at, 
                                     delivery_info.* 
                                     FROM user 
                                     LEFT JOIN delivery_info 
                                        ON delivery_info.user_id = user.id 
                                     WHERE user.id = ?", $userId);
        return $result;
    }

    function modifyUserData($userId, $userData) {
        include_once "./auth/init.php";
        var_dump($userData);
    }

?>