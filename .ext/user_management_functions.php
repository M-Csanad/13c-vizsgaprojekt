<?php
/**
  * Lekéri a felhasználó adatait
  *
  * A sütit a hashelt azonosítója alapján kitörli az adatbázisból, majd a felhasználó gépéről. Ha nem adunk meg azonosítót, akkor a függvény megnézi a sütit és a $_SESSION-t, hogy van-e azonosító.
  *
  * @param int $userId A felhasználó azonosítója
  *
  * @return result
  */
function getUserData($userId = null) {
    include_once "init.php";

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['expires_at']) && $_SESSION['expires_at'] < time()) {
        session_destroy();
        session_regenerate_id();

        return new Result(Result::ERROR, "A munkamenet lejárt vagy érvénytelen.");
    }
    
    if (!isset($userId) && !isset($_COOKIE["rememberMe"]) && !isset($_SESSION["user_id"])) {
        return new Result(Result::EMPTY, "Nem található azonosító az eszközön.");
    }
    
    // Ha paraméternek megadtunk egy userId-t, akkor csak lekérdezzük az adatait.
    if (isset($userId) || isset($_SESSION["user_id"])) {
        
        $sessionUserId = $_SESSION["user_id"];
        return selectData("SELECT CAST(user.id AS INT) AS id, user.email, 
            user.user_name, user.role, user.first_name, 
            user.last_name, user.pfp_uri,user.created_at 
            FROM user WHERE user.id = ?", (isset($userId)) ? $userId : $sessionUserId, "i");
    }
    else if (isset($_COOKIE["rememberMe"])) { // Ha nem userId-t adunk meg, akkor leellenőrizzük a sütit-t.
        
        $cookieToken = $_COOKIE["rememberMe"];
        $result = selectData("SELECT CAST(user.id AS INT) AS id, user.email, user.user_name, user.role, user.first_name, 
                                    user.last_name, user.pfp_uri, user.created_at, user.cookie_expires_at
                                FROM user
                                WHERE user.cookie_id = ? 
                                AND user.cookie_expires_at > ?",
                                [$cookieToken, time()], "si");

        if ($result->isSuccess()) {
            $user = $result->message[0];

            setSessionData($user);
            return $result;
        } else {
            return new Result(Result::ERROR, "A használt süti lejárt vagy érvénytelen.");
        }
    }
}

function modifyUserData($userId, $userData) {
    include_once "init.php";
    var_dump($userData);
}

function modifyRole($userId, $role) {
    include_once "init.php";
    $result = updateData("UPDATE user SET user.role = ? WHERE user.id = ?", [$role, $userId], "si");

    return $result;
}