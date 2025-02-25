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

/**
 * Ellenőrzi, hogy egy terméknek van-e elegendő készlete a kért mennyiséghez
 * @param int $productId A termék azonosítója, amelyet ellenőrizni kell
 * @param int $quantity A mennyiség, amelyet a készlethez viszonyítva ellenőrizni kell
 * @return Result Siker, ha elegendő a készlet, Hiba, ha nincs elég vagy a lekérdezés sikertelen
 */
function checkProductStock($productId, $quantity) {
    $result = selectData(
        "SELECT stock FROM product WHERE id = ?", 
        [$productId], 
        "i"
    );

    if ($result->isError()) {
        return new Result(Result::ERROR, "Adatbázis hiba!");
    }

    if ($result->isEmpty()) {
        return new Result(Result::ERROR, "A termék nem található!");
    }

    $stock = $result->message[0]['stock'];
    if ($quantity > $stock || $quantity < 1) {
        return new Result(Result::ERROR, "Nincs elég készlet! (Elérhető: $stock db)");
    }

    return new Result(Result::SUCCESS, null);
}

function getCurrentQuantity($productId) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }

    foreach ($_SESSION['cart'] as $item) {
        if ($item['product_id'] === $productId) {
            return (int)$item['quantity'];
        }
    }

    return 0;
}