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
                $typeString .= gettype($parameter)[0];
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
        return $db -> error;
    }
}

function updateData($query, $parameters) {
    try {
        include_once "./auth/init.php";
        $db = createConnection();
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        
        $statement = $db -> prepare($query);
        
        $typeString = '';
        foreach ($parameters as $parameter) {
            $typeString .= gettype($parameter)[0];
        }
        
        $statement -> bind_param($typeString, ...$parameters);
        $statement -> execute();

        $db -> close();
        return true;
    }
    catch(Exception $e) {
        return $db -> error;
    }
}

?>