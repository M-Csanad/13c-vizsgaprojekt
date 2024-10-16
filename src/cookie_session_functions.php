<?php

function bindCookie($user) {
    include "./db_connect.php";
    $cookieToken = hash('sha256', bin2hex(random_bytes(32)));
    $expireTime = 5 * 60;
    $expireUnix = time() + $expireTime;

    setcookie('rememberMe', $cookieToken, $expireUnix, '/', '', false, false); // 1 órás süti létrehozása

    $cookieStatement = $db -> prepare("UPDATE user SET cookie_id = ?, cookie_expires_at = ? WHERE user.id = ?");
    $cookieStatement -> bind_param("sii", $cookieToken, $expireUnix, $user['id']);
    $successfulCookie = $cookieStatement -> execute();

    // TODO - Szükséges ellenőrizni, hogy sikeres-e ????????
    if ($successfulCookie) {
        return true;
    }
    else {
        // Ha sikertelen az adatbázisba való feltöltés, akkor töröljük a sütit.
        removeCookie($cookieToken);
        return $successfulCookie -> error;
    }
}

function removeCookie($cookieToken) {
    include "./db_connect.php";

    unset($_COOKIE['rememberMe']);
    setcookie('rememberMe', '', time() - 3600, '/');
    echo $cookieToken;
    $cookieStatement = $db -> prepare("UPDATE user SET cookie_id = NULL, cookie_expires_at = NULL WHERE user.cookie_id = ?");
    $cookieStatement -> bind_param("i", $cookieToken);
    $successfulCookie = $cookieStatement -> execute();
}

function setSessionData($user) {
    // session_regenerate_id(true); // Session fixation támadás ellen, hogy ne lehessen megjósolni a sessionID-t :)
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['role'] = $user['role'];
}

?>