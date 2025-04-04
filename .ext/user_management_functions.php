<?php
include_once "init.php";

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

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION["expires_at"]) && $_SESSION["expires_at"] < time()) {
        session_unset();
        session_destroy();
        setcookie("PHPSESSID", "", time() - 3600, "/");
        session_start();
        session_regenerate_id(true);

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
            user.last_name, avatar.uri AS pfp_uri, user.created_at, user.phone
            FROM user 
            INNER JOIN avatar ON user.avatar_id=avatar.id
            WHERE user.id = ?", (isset($userId)) ? $userId : $sessionUserId, "i");
    }
    else if (isset($_COOKIE["rememberMe"])) { // Ha nem userId-t adunk meg, akkor leellenőrizzük a sütit-t.
        
        $cookieToken = $_COOKIE["rememberMe"];
        $result = selectData("SELECT CAST(user.id AS INT) AS id, user.email, user.user_name, user.role, user.first_name, 
                                    user.last_name, avatar.uri AS pfp_uri, user.created_at, user.cookie_expires_at,
                                    user.phone
                                FROM user
                                INNER JOIN avatar ON user.avatar_id=avatar.id
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

function getProfileUri($userId) {
    return selectData("SELECT avatar.id, avatar.uri FROM user INNER JOIN avatar on user.avatar_id=avatar.id WHERE user.id=?", $userId, "i");
}

// Hashelt jelszó lekérdezése a felhasználó azonosító alapján
function getHashedPassword($userId) {
    return selectData("SELECT user.password_hash FROM user WHERE user.id=?", $userId, "i");
}

// Jelszó módosítása
function modifyPassword($userId, $new) {
    return updateData("UPDATE user SET password_hash=? WHERE id=?", [$new, $userId], "si");
}

// Jelszó visszaállítása
function sendPasswordResetEmail($user) {

    $token = bin2hex(random_bytes(32));
    $expires = time() + 60 * 30;

    $result = updateData("UPDATE user SET reset_token=?, reset_token_expires_at=? WHERE id=?", [$token, $expires, $user["id"]], "sii");

    if ($result->isSuccess()) {
        $mail = [
            "subject" => "Jelszó visszaállítás",
            "body" => Mail::getMailBody("reset_password", $user["first_name"], ['token' => $token]),
            "alt" => Mail::getMailAltBody("reset_password", $user["first_name"], ['token' => $token])
        ];

        $mailer = new Mail();
        $mailer->sendTo($user, $mail, true);
    }

    return $result;
}

function resetUserPassword(int $userId, string $password): Result {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    return updateData(
        "UPDATE user SET password_hash = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?",
        [$passwordHash, $userId],
        "si"
    );
}

// Jogosultság módosítása
function modifyRole($userId, $role) {
    return updateData("UPDATE user SET user.role = ? WHERE user.id = ?", [$role, $userId], "si");
}

// Személyes adatok módodítása
function updatePersonalDetails($column, $data, $userId, $type) {
    return updateData("UPDATE user SET $column=? WHERE id=?", [$data, $userId], $type);
}

// Felhasználó törlése
function deleteUser($userId) {
    return updateData("DELETE FROM user WHERE id=?", $userId, "i");
}