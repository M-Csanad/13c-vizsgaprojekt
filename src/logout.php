<?php
    session_start();
    session_destroy();
    
    if (isset($_COOKIE['rememberMe'])) {
        include "./cookie_session_functions.php";
        removeCookie($_COOKIE['rememberMe']);
    }

    session_start();
    $_SESSION['successful_logout'] = true;

    header('Location: ./');
    exit;
?>