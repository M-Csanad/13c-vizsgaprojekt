<?php
include_once "init.php";

function newOrder($data) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return new Result(Result::ERROR, "A kosár üres");
    
    // Kosár adatok megszerzése
    $cart = $_SESSION['cart'];
    
    // Készlet ellenőrzése
    $result = checkStocks($cart);
    if (!$result->isSuccess()) return $result;

    // Feltöltés az order táblába
    $result = createOrderRow($data);
    $orderId = $result->lastInsertId;

    if (!$result->isSuccess()) {
        return $result;
    }

    // Feltöltés az order_items táblába
    $result = uploadCartItemsToDatabase($orderId, $cart);
    if (!$result->isSuccess()) {
        return $result;
    }

    return new Result(Result::SUCCESS, "Rendelés feltöltve.");
}

function getAddressFromComponents($zip, $city, $streetHouse) {
    return "$zip $city, $streetHouse";
}

function createOrderRow($data) {
    $result = getUserData();
    $user = null;
    $isLoggedIn = false;
    if ($result->isSuccess()) {
        $user = $result->message[0];
        $isLoggedIn = true;
    }

    $data['customer']["userId"] = $isLoggedIn ? $user['id'] : null;

    // A rendelés mezőinek és értékeinek kigyűjtése
    $fields = [];
    $values = [];
    $typeString = '';

    if ($isLoggedIn) {
        $fields[] = "user_id";
        $values[] = $data['customer']['userId'];
        $typeString .= 'i';
    }
    array_push($fields, "email", "phone", "first_name", "last_name", "delivery_address", "completed_at");

    array_push($values, 
        $data['customer']['email'], $data['customer']['phone'], 
        $data['customer']['firstName'], $data['customer']['lastName'], 
        getAddressFromComponents($data['delivery']['zipCode'], $data['delivery']['city'], $data['delivery']['streetHouse']),
        date("Y-m-d H:i:s", time())
    );
    $typeString .= 'ssssss';

    // Céges rendelés esetén
    if ($data['purchaseType'] === "Cégként rendelek") {
        array_push($fields, 'company_name', 'tax_number');
        array_push($values, $data['company']['companyName'], $data['company']['taxNumber']);
        $typeString .= 'ss';
    }
    
    // Ha a számlázási cím és a szállítási cím nem egyezik meg
    if ($data['billing']['sameAddress'] === false) {
        array_push($fields, 'billing_address');
        array_push($values, getAddressFromComponents($data['billing']['zipCode'], $data['billing']['city'], $data['billing']['streetHouse']));
        $typeString .= 's';
    }
    
    // Adatok feltöltése
    $fieldString = implode(", ", $fields);
    $wildCardString = implode(", ", array_fill(0, count($fields), '?'));
    return updateData("INSERT INTO `order`($fieldString) VALUES ($wildCardString);", $values, $typeString);
}

function uploadCartItemsToDatabase($orderId, $cart) {
    // Kosár termékek feltöltése az adatbázisba
    $values = [];
    $typeString = '';
    foreach ($cart as $item) {
        $values[] = $orderId;
        $values[] = $item['product_id'];
        $values[] = $item['quantity'];
        $typeString .= 'iii';
    }

    // Készítünk egy wildcard stringet az összes sorhoz
    $wildCardString = implode(', ', array_fill(0, count($cart), '(?, ?, ?)'));

    // Adatok feltöltése az adatbázisba
    $query = "INSERT INTO order_item (order_id, product_id, quantity) VALUES $wildCardString;";
    $result = updateData($query, $values, $typeString);

    if (!$result->isSuccess()) {
        return new Result(Result::ERROR, "Hiba történt a kosár feltöltésekor.");
    }

    return new Result(Result::SUCCESS, "Sikeres feltöltés az adatbázisba.");
}

// Készlet ellenőrzése
function checkStocks($cart) {
    $productIds = array_column($cart, 'product_id');
    $wildCardString = implode(', ', array_fill(0, count($productIds), '?'));
    $typeString = str_repeat('i', count($productIds));

    // Készlet lekérdezése
    $result = selectData("SELECT product.id, product.stock FROM product WHERE product.id IN ($wildCardString);", $productIds, $typeString);
    if (!$result->isSuccess()) return $result;
    $stocks = $result->message;

    // Kosaran végigmegyünk, minden terméket leellenőrzünk
    foreach ($cart as $item) {
        $currentStock = array_filter($stocks, function ($x) use ($item) { return $x['id'] === $item['product_id']; });
        $currentStock = reset($currentStock);

        if ($item['quantity'] > $currentStock['stock']) return new Result(Result::ERROR, "A termék a megadott mennyiségben már nem elérhető.");
    }

    return new Result(Result::SUCCESS, "A készlet megfelelő minden termékre nézve.");
}