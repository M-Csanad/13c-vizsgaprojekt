<?php
include_once "init.php";

function clearCart() {
    $user = null;
    $isLoggedIn = false;
    $result = getUserData();
    if ($result->isSuccess()) {
        $user = $result->message[0];
        $isLoggedIn = true;
    }

    if ($isLoggedIn) {
        $result = updateData("DELETE FROM cart WHERE cart.user_id=?", $user['id'], 'i');
        if (!$result->isSuccess()) {
            return $result;
        }
    }

    removeCartCookieSession();

    return new Result(Result::SUCCESS, "Sikeres kosár törlés");
}