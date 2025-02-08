<?php
include_once "init.php";

// CREATE
function uploadAutofill($data) {
    $type = $data['type'];
    $result = updateData("INSERT INTO autofill_$type(user_id, name, zip, city, street_house) VALUES (?, ?, ?, ?, ?);", [$data['user_id'], $data['autofill-name'], intval($data['zip']), $data['city'], $data['street-house']], 'isiss');

    if (!$result->isSuccess()) {
        return $result;
    }

    return selectData("SELECT id, name, zip, city, street_house FROM autofill_$type WHERE id=?", $result->lastInsertId, 'i');
}

// READ
function getAutofill($type, $userId) {
    if (in_array($type, ["billing", "delivery"]) && is_int($userId) && $userId > 0) {
        return selectData("SELECT id, name, zip, city, street_house FROM autofill_$type WHERE user_id=?;", $userId, 'i');
    }
    else return new Result(Result::ERROR, "Hibás paraméter.");
}

function getAllAutofill($userId) {
    if (is_int($userId) && $userId > 0) {

        $types = ["billing", "delivery"];
        $results = [];
        
        foreach ($types as $type) {
            if (in_array($type, ["billing", "delivery"])) {
                $data = selectData("SELECT id, name, zip, city, street_house FROM autofill_$type WHERE user_id=?;", $userId, 'i');

                if ($data->isEmpty()) {
                    $results[$type] = [];
                }
                else {
                    $results[$type] = $data->message;
                }
            }
        }
        
        return new Result(Result::SUCCESS, $results);
    }
    else {
        return new Result(Result::ERROR, "Hibás paraméter");
    }
}

function getAutofillFromId($id, $type) {
    if (in_array($type, ["billing", "delivery"]) && is_int($id) && $id > 0) {
        return selectData("SELECT id, name, zip, city, street_house FROM autofill_$type WHERE id=?;", $id, "i");
    }
    else {
        return new Result(Result::ERROR, "Hibás paraméter");
    }
}

// UPDATE
function updateAutofill($values) {
    $type = $values["type"];
    $result = updateData("UPDATE autofill_$type SET name=?, zip=?, city=?, street_house=? WHERE id=? AND user_id=?;", [
        $values["autofill-name"],
        $values["zip"],
        $values["city"],
        $values["street-house"],
        $values["id"],
        $values["user_id"]
    ], "sissii");
    return $result;
}

// DELETE
function deleteAutofill($id, $type) {
    return updateData("DELETE FROM autofill_$type WHERE id=?", $id, 'i');
}