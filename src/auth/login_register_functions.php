<?php
function register($username, $password, $email) {
    if (!$username || !$password || !$email) {
        return "Kérjük töltse ki az összes mezőt!";
    }
    
    include "./auth/db_connect.php";

    session_start();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $registerStatement = $db -> prepare("INSERT INTO user (user_name, email, password_hash) VALUES (?, ?, ?)");
    $registerStatement -> bind_param("sss", $username, $email, $passwordHash);
    
    try {
        $successfulRegistration = $registerStatement -> execute();
        return true;
        
    } catch (Exception $error) {
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
}

function login($username, $password, $rememberMe) {
    if (!$username || !$password) {
        return "Kérjük töltse ki az összes mezőt!";
    }
    
    include "./auth/db_connect.php";
    include "./auth/cookie_session_functions.php";

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
    include "./auth/db_connect.php";

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