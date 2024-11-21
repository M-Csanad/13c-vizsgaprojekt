<?php

function selectData($query, $parameters) {
    try {
        include_once "init.php";
        $db = createConnection();

        $statement = $db -> prepare($query);

        if ($parameters) {
            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }
            
            $typeString = '';
            foreach ($parameters as $parameter) {
                if (is_null($parameter)) {
                    $typeString .= 's';
                } else {
                    $typeString .= gettype($parameter)[0];
                }
            }
            
            $statement -> bind_param($typeString, ...$parameters);
        }
        $statement -> execute();
        $result = $statement -> get_result();
        $db -> close();

        if ($result -> num_rows == 1) {
            return ["message" => $result -> fetch_assoc(), "type" => "SUCCESS", "contentType" => "ASSOC"];
        }
        if ($result -> num_rows > 0) {
            return ["message" => $result -> fetch_all(MYSQLI_ASSOC), "type" => "SUCCESS", "contentType" => "ARRAY"];
        }
        else {
            return ["message" => "Nincs találat!", "type" => "EMPTY"];
        }
    }
    catch(Exception $e) {
        return ["message" => $e, "type" => "ERROR"];
    }
}

function updateData($query, $parameters) {
    try {
        include_once "init.php";
        $db = createConnection();
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        
        $statement = $db -> prepare($query);
        
        $typeString = '';
        foreach ($parameters as $parameter) {
            if (is_null($parameter)) {
                $typeString .= 's';
            } else {
                $typeString .= gettype($parameter)[0];
            }
        }
        
        $statement -> bind_param($typeString, ...$parameters);
        $statement -> execute();

        $db -> close();
        if ($statement -> affected_rows > 0) {
            if (str_contains($query, "INSERT")) {
                return ["message" => $statement -> insert_id, "type" => "SUCCESS", "contentType" => "INT"];
            }
            else return ["message" => true, "type" => "SUCCESS"];
        }
        else return ["message" => false, "type" => "NO_AFFECT"];
    }
    catch(Exception $e) {
        return ["message" => $e, "type" => "ERROR"];
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