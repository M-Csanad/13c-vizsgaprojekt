<?php

function bindCookie($user)
{
    include_once "init.php";
    $cookieToken = hash('sha256', bin2hex(random_bytes(32)));
    $expireTime = 5 * 60; // Lejárás ideje másodpercben
    $expireUnix = time() + $expireTime;
    $result = updateData("UPDATE user 
                          SET cookie_id = ?, 
                          cookie_expires_at = ? 
                          WHERE user.id = ?", [$cookieToken, $expireUnix, $user['id']]);

    if ($result === true) {
        setcookie('rememberMe', $cookieToken, $expireUnix, '/', '', false, false); // 5 perces süti létrehozása
    }
    else {
        echo "<div class='error'>$result</div>";
    }
}

function removeCookie($cookieToken)
{
    include_once "init.php";
    $result = updateData("UPDATE user 
                          SET cookie_id = NULL, 
                          cookie_expires_at = NULL 
                          WHERE user.cookie_id = ?", $cookieToken);
    
    if ($result === true) {
        unset($_COOKIE['rememberMe']);
        setcookie('rememberMe', '', time() - 3600, '/');
    }
    else {
        echo "<div class='error'>$result</div>";
    }
}

function setSessionData($user)
{
    // session_regenerate_id(true); // Session fixation támadás ellen, hogy ne lehessen megjósolni a sessionID-t :)
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['role'] = $user['role'];
}

?>

