<?php
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