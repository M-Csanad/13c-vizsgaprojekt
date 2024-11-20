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
            return $result -> fetch_assoc();
        }
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }
        else {
            return "Nincs találat!";
        }
    }
    catch(Exception $e) {
        return $e;
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
                return $statement -> insert_id;
            }
            else return true;
        }
        else return false;
    }
    catch(Exception $e) {
        return $e;
    }
}

function isError($result) {
    return $result["type"] == "ERROR";
}

function isSuccess($result) {
    return $result["type"] == "SUCCESS";
}

?>