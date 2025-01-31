<?php
include_once "init.php";

function uploadAutofill($data) {
    $type = $data['type'];
    return updateData("INSERT INTO billing_$type(user_id, name, zip, city, street_house) VALUES (?, ?, ?, ?, ?);", [], 'isiss');
}