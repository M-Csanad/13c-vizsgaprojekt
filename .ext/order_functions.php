<?php
include_once "init.php";

function newOrder($data) {
    $result = getUserData();
    $user = null;
    $isLoggedIn = false;
    if ($result->isSuccess()) {
        $user = $result->message[0];
        $isLoggedIn = true;
    }

    $data['customer']["userId"] = $isLoggedIn ? $user['id'] : null;

    // A rendelés mezőinek és értékeinek kigyűjtése
    $fields = ["user_id", "email", "phone", "first_name", "last_name", "delivery_address", "completed_at"];
    $values = [
        $data['customer']['userId'], $data['customer']['email'], $data['customer']['phone'], 
        $data['customer']['firstName'], $data['customer']['lastName'], 
        getAddressFromComponents($data['delivery']['zipCode'], $data['delivery']['city'], $data['delivery']['street'], $data['delivery']['houseNumber']),
        date("Y-m-d H:i:s", time())
    ];
    $typeString = 'issssss';

    // Céges rendelés esetén
    if ($data['purchaseType'] === "Cégként rendelek") {
        array_push($fields, 'company_name', 'tax_number');
        array_push($values, $data['company']['companyName'], $data['company']['taxNumber']);
        $typeString .= 'ss';
    }
    
    // Ha a számlázási cím és a szállítási cím nem egyezik meg
    if ($data['billing']['sameAddress'] === false) {
        array_push($fields, 'billing_address');
        array_push($values, getAddressFromComponents($data['billing']['zipCode'], $data['billing']['city'], $data['billing']['street'], $data['billing']['houseNumber']));
        $typeString .= 's';
    }
    
    $fieldString = implode(", ", $fields);
    $wildCardString = implode(', ', array_fill(0, count($fields), '?'));
    
    $result = updateData("INSERT INTO `order`($fieldString) VALUES ($wildCardString);", $values, $typeString);
    return $result;
}

function copyItemsFromCart() {

}

function getAddressFromComponents($zip, $city, $street, $houseNum) {
    return "$zip $city, $street utca $houseNum";
}