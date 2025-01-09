<?php
include_once $_SERVER["DOCUMENT_ROOT"] . '/config.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/../../../.ext/init.php';

$db = db_connect();

var_dump(selectData("SELECT * FROM product;")->toJSON());
