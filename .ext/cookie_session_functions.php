<?php

/**
  * **Elhelyeziaz EMlékezz rám sütit** a felhasználó számítógépén, és az azonosítót feltölti az adatbázisba.
  *
  * A süti elhelyezése után az azonosító és a érvényességi idő feltöltésre kerülnek a megadott *user.id*-vel rendelkező felhasználóhoz.
  *
  * @param array $userId A felhasználó, akihez a sütit szeretnénk kapcsolni.
  *
  * @return Result
  */
function bindCookie($userId): Result
{
    include_once "init.php";
    $cookieToken = hash('sha256', bin2hex(random_bytes(32)));
    $expireTime = 3 * 24 * 60 * 60; // Lejárás ideje másodpercben
    $expireUnix = time() + $expireTime;
    $result = updateData("UPDATE user 
                          SET cookie_id = ?, 
                          cookie_expires_at = ? 
                          WHERE user.id = ?", [$cookieToken, $expireUnix, $userId], "sii");

    if ($result->isSuccess()) {
        setcookie('rememberMe', $cookieToken, $expireUnix, '/', '', false, false); // 1 hétig érvényes süti létrehozása
        return new Result(Result::SUCCESS, "Sikeres süti felvitel.");
    }
    else {
        return $result;
    }
}

/**
  * Kitörli az Emlékezz rám sütit az adatbázisból, illetve a felhasználó gépéről.
  *
  * A sütit a hashelt azonosítója alapján kitörli az adatbázisból, majd a felhasználó gépéről.
  *
  * @param string $cookieToken A süti egyedi, hashelt azonosítója
  *
  * @return void
  */
function removeCookie($cookieToken)
{
    include_once "init.php";

    // A süti törlése a felhasználó gépéről
    unset($_COOKIE['rememberMe']);
    setcookie('rememberMe', '', time() - 3600, '/');

    // A süti törlése az adatbázisból
    $result = updateData("UPDATE user  SET cookie_id = NULL,  cookie_expires_at = NULL WHERE user.cookie_id = ?", $cookieToken, "s");
    
    return ($result->isSuccess()) ? ["message" => "Sikeres süti törlés.", "type" => "SUCCESS"] : $result;
}

function setSessionData($user)
{
    // session_regenerate_id(true); // Session fixation támadás ellen, hogy ne lehessen megjósolni a sessionID-t :)
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['expires_at'] = time() + 3 * 24 * 60 * 60;
}