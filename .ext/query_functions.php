<?php

function selectData($query, $parameters = null, $typeString = null) {
    try {
        if (!$query) {
            return ["message" => "Üres paraméter(ek).", "type" => "ERROR"];
        }

        include_once "init.php";
        $db = db_connect();

        if ($parameters) {

            if (!$typeString) {
                return ["message" => "Előkészített lekérdezéshez adja meg a paramétertípusokat.", "type" => "ERROR"];
            }

            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }

            $statement = $db->prepare($query);
            if (!$statement) {
                return ["message" => $db->error, "type" => "ERROR"];
            }

            $types = '';
            foreach ($parameters as $parameter) {
                $types .= is_null($parameter) ? 's' : gettype($parameter)[0];
            }

            if ($types != $typeString) {
                return ["message" => "A típus string nem egyezik meg a tényleges típusokkal.".$query, "type" => "ERROR"];
            }

            $statement->bind_param($typeString, ...$parameters);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
        } else {
            $result = $db->query($query);
            if ($db->errno) {
                return ["message" => $db->error, "type" => "ERROR"];
            }
        }

        db_disconnect($db);

        if ($result->num_rows === 1) {
            return ["message" => $result->fetch_assoc(), "type" => "SUCCESS", "contentType" => "ASSOC"];
        }

        if ($result->num_rows > 0) {
            return ["message" => $result->fetch_all(MYSQLI_ASSOC), "type" => "SUCCESS", "contentType" => "ARRAY"];
        }

        return ["message" => "Nincs találat!", "type" => "EMPTY"];
    } catch (Exception $e) {
        return ["message" => $e->getMessage(), "code" => $e->getCode(), "type" => "ERROR"];
    }
}


function updateData($query, $parameters = null, $typeString = null) {
    try {
        if (!$query || !$typeString) return ["message" => "Üres paraméter(ek).", "type" => "ERROR"];

        include_once "init.php";
        $db = db_connect();

        if ($parameters) {
            if (!$typeString) {
                return ["message" => "Előkészített lekérdezéshez adja meg a paramétertípusokat.", "type" => "ERROR"];
            }

            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }

            $statement = $db->prepare($query);

            if (!$statement) {
                return ["message" => $db->error, "type" => "ERROR"];
            }

            $typeString = '';
            foreach ($parameters as $parameter) {
                $typeString .= is_null($parameter) ? 's' : gettype($parameter)[0];
            }

            $statement->bind_param($typeString, ...$parameters);
            $statement->execute();
            $affectedRows = $statement->affected_rows;
            $insertId = $statement->insert_id;

            $statement->close();
        } else {
            $db->query($query);

            if ($db->errno) {
                return ["message" => $db->error, "type" => "ERROR"];
            }

            $affectedRows = $db->affected_rows;
            $insertId = $db->insert_id;
        }

        db_disconnect($db);

        if ($affectedRows > 0) {
            if (str_contains($query, "INSERT")) {
                return ["message" => $insertId, "type" => "SUCCESS", "contentType" => "INT"];
            }
            return ["message" => true, "type" => "SUCCESS"];
        }
        return ["message" => false, "type" => "NO_AFFECT"];
    } catch (Exception $e) {
        return ["message" => $e->getMessage(), "code" => $e->getCode(), "type" => "ERROR"];
    }
}


function isError($result) {
    return $result["type"] == "ERROR";
}

function isSuccess($result) {
    return $result["type"] == "SUCCESS";
}

function typeOf($result, $resultType) {
    if (!isset($result["type"])) return false;
    return $result["type"] === $resultType;
}
?>