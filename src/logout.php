<?php
    session_start();
    session_destroy();
    
    session_start();
    $_SESSION['successful_logout'] = true;

    header('Location: ./');
    exit;
?>