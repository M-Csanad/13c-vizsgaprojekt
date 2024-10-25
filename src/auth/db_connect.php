<?php 
    function createConnection() {
        return new mysqli('localhost:3307', 'root', '', 'florens_botanica');
    }
?>