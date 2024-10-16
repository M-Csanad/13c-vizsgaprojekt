<?php
function register($username, $password, $email) {
    if (!$username || !$password || !$email) {
        return "Kérjük töltse ki az összes mezőt!";
    }
    
    include "./db_connect.php";

    session_start();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $registerStatement = $db -> prepare("INSERT INTO user (user_name, email, password_hash) VALUES (?, ?, ?)");
    $registerStatement -> bind_param("sss", $username, $email, $passwordHash);
    $successfulRegistration = $registerStatement -> execute();

    if ($successfulRegistration) {
        return "Sikeres regisztráció!";
    }
    else {
        return $registerStatement -> error;
    }
}

function login($username, $password, $rememberMe) {
    if (!$username || !$password) {
        return "Kérjük töltse ki az összes mezőt!";
    }
    
    include "./db_connect.php";
    include "./cookie_session_functions.php";

    session_start();
    $user = authenticate_user($username, $password);
    
    if ($user != null) {
        setSessionData($user);
        if ($rememberMe) {
            bindCookie($user);
        }
        return true;
    } 
    else {
        return "Érvénytelen felhasználónév, vagy jelszó.";
    }
}

function authenticate_user($username, $password) {
    include "./db_connect.php";

    $loginStatement = $db -> prepare("SELECT COUNT(*) as num, user.password_hash, user.role, user.id, user.user_name FROM user WHERE user.user_name = ?");
    $loginStatement -> bind_param("s", $username);

    $successfulLogin = $loginStatement -> execute();
    $result = $loginStatement -> get_result();
    $data = $result -> fetch_assoc();

    if (!$data['num'] || !password_verify($password, $data['password_hash'])) {
        return null;
    }
    else {
        return $data;
    }
}
?>