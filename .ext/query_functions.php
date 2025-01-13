<?php
include_once 'classes/queryresult.php';
include_once "init.php";

function getTypeString($params) {
    $types = '';
    foreach ($params as $p) {
        $types .= is_null($p) ? 's' : gettype($p)[0];
    }
    return mb_strtolower($types);
}

function validateQueryAndParameters($query, $parameters, $typeString) {
    if (!$query) {
        return new QueryResult(Result::ERROR, "Üres lekérdezés.", $query, $parameters);
    }

    if ($parameters && !$typeString) {
        return new QueryResult(
            type: Result::ERROR, 
            message: "Előkészített lekérdezéshez adja meg a paramétertípusokat.", 
            query: $query,
            params: $parameters
        );
    }

    if (is_array($parameters)) {
        if (substr_count($query, '?') !== count($parameters)) {
            return new QueryResult(
                type: Result::ERROR, 
                message: "A paraméterek száma nem egyezik meg pontosan a várt számmal.", 
                query: $query,
                params: $parameters
            );
        }
    }
    else {
        if (substr_count($query, '?') !== 1) {
            return new QueryResult(
                type: Result::ERROR, 
                message: "A paraméterek száma nem egyezik meg pontosan a várt számmal.", 
                query: $query,
                params: $parameters
            );
        }
    }

    return new Result(Result::SUCCESS, "A bemeneti adatok megfelelőek.");
}

function executeStatement($db, $query, $parameters, $typeString) {
    if (getTypeString($parameters) !== $typeString) {
        return new QueryResult(
            type: Result::ERROR,
            message: "A típus string nem egyezik meg a tényleges típusokkal. Kapott: ".getTypeString($parameters),
            query: $query, 
            params: $parameters
        );
    }

    $statement = $db->prepare($query);
    if (!$statement) {
        return new QueryResult(
            type: Result::ERROR,
            message: $db->error,
            code: $db->errno,
            query: $query,
            params: $parameters
        );
    }

    $statement->bind_param($typeString, ...$parameters);
    $statement->execute();
    return $statement;
}

function selectData($query = null, $parameters = null, $typeString = null): QueryResult {
    try {
        $validationResult = validateQueryAndParameters($query, $parameters, $typeString);
        if ($validationResult->isError()) {
            return $validationResult;
        }

        $db = db_connect();
        if ($parameters) {
            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }

            $statement = executeStatement($db, $query, $parameters, $typeString);
            if ($statement instanceof QueryResult) {
                db_disconnect($db);
                return $statement;
            }
            
            $result = $statement->get_result();
            $statement->close();
        } else {
            $result = $db->query($query);
            if ($db->errno) {
                db_disconnect($db);
                return new QueryResult(
                    type: Result::ERROR,
                    message: $db->error,
                    code: $db->errno,
                    query: $query
                );
            }
        }

        db_disconnect($db);

        if ($result->num_rows > 0) {
            return new QueryResult(
                type: Result::SUCCESS,
                message: $result->fetch_all(MYSQLI_ASSOC),
                query: $query,
                params: $parameters
            );
        }

        return new QueryResult(
            type: Result::EMPTY,
            message: "Nincs találat!",
            query: $query,
            params: $parameters
        );
    } catch (Exception $e) {
        return new QueryResult(
            type: Result::ERROR,
            message: $e->getMessage(),
            code: $e->getCode(),
            query: $query,
            params: $parameters
        );
    }
}


function updateData($query, $parameters = null, $typeString = null): QueryResult {
    try {
        $validationResult = validateQueryAndParameters($query, $parameters, $typeString);
        if ($validationResult->isError()) {
            return $validationResult;
        }

        $db = db_connect();
        if ($parameters) {
            if (!is_array($parameters)) {
                $parameters = [$parameters];
            }

            $statement = executeStatement($db, $query, $parameters, $typeString);
            if ($statement instanceof QueryResult) {
                db_disconnect($db);
                return $statement;
            }

            $affectedRows = $statement->affected_rows;
            $insertId = $statement->insert_id;
            $statement->close();
        } else {
            $db->query($query);

            if ($db->errno) {
                db_disconnect($db);
                return new QueryResult(
                    type: Result::ERROR,
                    message: $db->error,
                    code: $db->errno,
                    query: $query
                );
            }

            $affectedRows = $db->affected_rows;
            $insertId = $db->insert_id;
        }

        db_disconnect($db);

        if ($affectedRows > 0) {
            if (str_contains($query, "INSERT")) {
                return new QueryResult(
                    type: Result::SUCCESS,
                    message: true,
                    query: $query,
                    params: $parameters,
                    affectedRows: $affectedRows,
                    lastInsertId: $insertId
                );
            }
            return new QueryResult(
                type: Result::SUCCESS,
                message: true,
                query: $query,
                params: $parameters,
                affectedRows: $affectedRows,
            );
        }
        return new QueryResult(
            type: Result::NO_AFFECT,
            message: false,
            query: $query,
            params: $parameters,
            affectedRows: $affectedRows,
        );
    } catch (Exception $e) {
        return new QueryResult(
            type: Result::ERROR,
            message: $e->getMessage(),
            code: $e->getCode(),
            query: $query,
            params: $parameters
        );
    }
}