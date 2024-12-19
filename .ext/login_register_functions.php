<?php
function register($username, $password, $email, $firstname, $lastname) {
    include_once "init.php";

    if (!$username || !$password || !$email || !$firstname || !$lastname) {
        return ["message" => "Kérjük töltse ki az összes mezőt!", "type" => "ERROR"];
    }
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $pfp = "https://ui-avatars.com/api/?name=$lastname+$firstname&background=9CB5A6&bold=true&format=svg";
    $result = updateData("INSERT INTO user (user_name, email, password_hash, first_name, last_name, pfp_uri) 
                          VALUES (?, ?, ?, ?, ?, ?)", [$username, $email, $passwordHash, $firstname, $lastname, $pfp], "ssssss");
                          
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
                          WHERE user.user_name = ?", $username, "s");

    if (typeOf($result, "SUCCESS")) {
        $user = $result["message"][0];
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