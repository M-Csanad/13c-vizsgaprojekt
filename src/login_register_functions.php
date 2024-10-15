<?php
session_start();

function register($username, $password, $email) {

    if (!$username || !$password || !$email) {
        return "Please fill in all the fields!";
    }
    
    $db = new mysqli('localhost', 'root', '', 'florens_botanica');

    if (!$db -> connect_errno) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $registerStatement = $db -> prepare("INSERT INTO user (user_name, email, password_hash) VALUES (?, ?, ?)");

        if ($registerStatement) {
            $registerStatement -> bind_param("sss", $username, $email, $passwordHash);
            $successfulRegistration = $registerStatement -> execute();

            if ($successfulRegistration) {
                return "Succesful registration!";
            }
            else {
                return $registerStatement -> error;
            }
        }
        else {
            return $db -> error;
        }
    }
    else {
        return $db -> connect_error;
    }
}

function login($username, $password) {
    if (!$username || !$password) {
        return "Please fill in all the fields!";
    }
    
    try {
        $db = new mysqli('localhost', 'root', '', 'florens_botanica');
    }
    catch (\Throwable $e) {
        return;
    }

    if (!$db -> connect_errno) {
        $loginStatement = $db -> prepare("SELECT COUNT(*) as num, user.password_hash, user.role FROM user WHERE user.user_name = ?");
        $loginStatement -> bind_param("s", $username);
        $loginStatement -> execute();
        $result = $loginStatement -> get_result();
        $data = $result -> fetch_assoc();

        if (!$data['num'] || !password_verify($password, $data['password_hash'])) {
            return "Invalid username or password.";
        }
        else {
            $_SESSION['user_name'] = $username;
            $_SESSION['role'] = $data['role'];
            return true;
        }
    }
    else {
        return "Something went wrong... Please try again later.";
    }
}

?>