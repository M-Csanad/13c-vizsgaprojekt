<?php 
    function createConnection() {
        include_once "./auth/db_config.php";
        return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }
?>