<?php
function register($username, $password, $email, $firstname, $lastname) {
    include_once "init.php";

    if (!$username || !$password || !$email || !$firstname || !$lastname) {
        return new Result(Result::ERROR, "Kérjük töltse ki az összes mezőt!");
    }
    
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $pfp = "https://ui-avatars.com/api/?name=$lastname+$firstname&background=9CB5A6&bold=true&format=svg";
    $result = updateData("INSERT INTO user (user_name, email, password_hash, first_name, last_name, pfp_uri) 
                          VALUES (?, ?, ?, ?, ?, ?)", [$username, $email, $passwordHash, $firstname, $lastname, $pfp], "ssssss");
                          
    if ($result->isError()) {
        $error = $result->message;
        if (str_contains($error, 'email')) {
            return new Result(Result::DENIED, "Ez az E-mail cím már foglalt.");
        }
        else if (str_contains($error, 'user_name')) {
            return new Result(Result::DENIED, "Ez a felhasználónév már foglalt.");
        }
        else {
            return ["message" => $error, "type" => "ERROR"];
        }
    }
    else return new Result(Result::SUCCESS, "Sikeres regisztráció.");
}

function login($username, $password, $rememberMe) {
    
    if (!$username || !$password) {
        return new Result(Result::ERROR, "Kérjük töltse ki az összes mezőt!");
    }
    
    include_once "init.php";

    $result = authenticate_user($username, $password);
    if (!$result->isSuccess()) {
        return $result;
    }

    $user = $result->message;
    setSessionData($user);
    if ($rememberMe) {
        $result = bindCookie($user["id"]);
        if (!$result->isSuccess()) {
            return $result;
        }
    }
    return new Result(Result::SUCCESS, "Sikeres süti felvitel.");
}

function authenticate_user($username, $password) {
    include_once "init.php";
    $result = selectData("SELECT user.password_hash, 
                          user.role, user.id, 
                          user.user_name
                          FROM user 
                          WHERE user.user_name = ?", $username, "s");

    if ($result->isSuccess()) {
        $user = $result->message[0];
        if (!password_verify($password, $user['password_hash'])) {
            return new Result(Result::DENIED, "Érvénytelen felhasználónév, vagy jelszó.");
        }
        else {
            return new Result(Result::SUCCESS, $user);
        }
    }
    else {
        return $result;
    }
}
?>