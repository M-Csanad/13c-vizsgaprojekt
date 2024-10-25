<?php
function register($username, $password, $email) {
    if (!$username || !$password || !$email) {
        return "Kérjük töltse ki az összes mezőt!";
    }
    
    include_once "./auth/init.php";

    session_start();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $result = updateData("INSERT INTO user (user_name, email, password_hash) 
                          VALUES (?, ?, ?)", [$username, $email, $passwordHash]);
    if ($result !== true) {
        if (str_contains($error, 'email')) {
            return "Ez az E-mail cím már foglalt.";
        }
        else if (str_contains($error, 'user_name')) {
            return "Ez a felhasználónév már foglalt.";
        }
        else {
            return $error;
        }
    }
    else return true;
}

function login($username, $password, $rememberMe) {
    if (!$username || !$password) {
        return "Kérjük töltse ki az összes mezőt!";
    }
    include_once "./auth/init.php";
    // include_once "./auth/db_connect.php";
    // include_once "./auth/cookie_session_functions.php";

    session_start();
    $user = authenticate_user($username, $password);
    
    if (is_array($user)) {
        setSessionData($user);
        if ($rememberMe) {
            bindCookie($user);
        }
        return true;
    } 
    else {
        if ($user == null || $user == "Nincs találat!") {
            return "Érvénytelen felhasználónév, vagy jelszó.";
        }
        else {
            return "<div class='error'>$user</div>";
        }
    }
}

function authenticate_user($username, $password) {
    // include_once "./auth/query_functions.php";
    include_once "./auth/init.php";
    $result = selectData("SELECT user.password_hash, 
                          user.role, user.id, 
                          user.user_name
                          FROM user 
                          WHERE user.user_name = ?", $username);
    if (is_array($result)) {
        $user = $result;
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }
        else {
            return $user;
        }
    }
    else {
        return $result;
    }
}
?>