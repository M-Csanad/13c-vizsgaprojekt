<?php

    function getUserData($userId) {
        include_once "init.php";
        $result = selectData("SELECT user.email, 
                                     user.user_name, 
                                     user.role, 
                                     user.first_name, 
                                     user.last_name, 
                                     user.pfp_uri,
                                     user.created_at 
                                     FROM user 
                                     WHERE user.id = ?", $userId);
        return $result;
    }

    function modifyUserData($userId, $userData) {
        include_once "init.php";
        var_dump($userData);
    }

    function modifyRole($userId, $role) {
        include_once "init.php";
        $result = updateData("UPDATE user SET user.role = ? WHERE user.id = ?", [$role, $userId]);

        return $result;
    }

?>