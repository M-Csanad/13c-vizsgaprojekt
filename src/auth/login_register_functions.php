<?php
function register($username, $password, $email) {
    if (!$username || !$password || !$email) {
        return ["message" => "Kérjük töltse ki az összes mezőt!", "type" => "ERROR"];
    }
    
    include_once "init.php";
    session_start();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $result = updateData("INSERT INTO user (user_name, email, password_hash) 
                          VALUES (?, ?, ?)", [$username, $email, $passwordHash]);
                          
    if (typeOf($result, "ERROR")) {
        $error = $result["message"];
        if (str_contains($error, 'email')) {
            return ["message" => "Ez az E-mail cím már foglalt.", "type" => "DENIED"];
        }
        else if (str_contains($error, 'user_name')) {
            return ["message" => "Ez a felhasználónév már foglalt.", "type" => "DENIED"];
        }
        else {
            return ["message" => $error, "type" => "ERROR"];
        }
    }
    else return ["message" => "Sikeres regisztráció.", "type" => "SUCCESS"];
}

function login($username, $password, $rememberMe) {
    if (!$username || !$password) {
        return ["message" => "Kérjük töltse ki az összes mezőt!", "type" => "ERROR"];
    }
    
    include_once "init.php";
    session_start();

    $result = authenticate_user($username, $password);
    if (!typeOf($result, "SUCCESS")) {
        return $result;
    }

    $user = $result["message"];
    setSessionData($user);
    if ($rememberMe) {
        $result = bindCookie($user["id"]);
        if (!typeOf($result, "SUCCESS")) {
            return $result;
        }
    }
    return ["message" => "Sikeres süti felvitel.", "type" => "SUCCESS"];
}

function authenticate_user($username, $password) {
    include_once "init.php";
    $result = selectData("SELECT user.password_hash, 
                          user.role, user.id, 
                          user.user_name
                          FROM user 
                          WHERE user.user_name = ?", $username);

    if (typeOf($result, "SUCCESS")) {
        $user = $result["message"];
        if (!password_verify($password, $user['password_hash'])) {
            return ["message" => "Érvénytelen felhasználónév, vagy jelszó.", "type" => "DENIED"];
        }
        else {
            return ["message" => $user, "type" => "SUCCESS"];
        }
    }
    else {
        return $result;
    }
}
?>