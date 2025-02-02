<?php
include_once "init.php";

function uploadAutofill($data) {
    $type = $data['type'];
    $result = updateData("INSERT INTO autofill_$type(user_id, name, zip, city, street_house) VALUES (?, ?, ?, ?, ?);", [$data['user_id'], $data['autofill-name'], intval($data['zip']), $data['city'], $data['street-house']], 'isiss');

    if (!$result->isSuccess()) {
        return $result;
    }

    return selectData("SELECT * FROM autofill_$type WHERE id=?", $result->lastInsertId, 'i');
}

function getAutofill($type, $userId) {
    if (in_array($type, ["billing", "delivery"]) && is_int($userId) && $userId > 0) {
        return selectData("SELECT * FROM autofill_$type WHERE user_id=?;", $userId, 'i');
    }
    else return new Result(Result::ERROR, "Hibás típus.");
}

function getAllAutofill($userId) {
    if (is_int($userId) && $userId > 0) {

        $types = ["billing", "delivery"];
        $results = [];
        
        foreach ($types as $type) {
            if (in_array($type, ["billing", "delivery"])) {
                $data = selectData("SELECT * FROM autofill_$type WHERE user_id=?;", $userId, 'i');

                $results[$type] = $data->message;
            }
        }
        
        return new Result(Result::SUCCESS, $results);
    }
}