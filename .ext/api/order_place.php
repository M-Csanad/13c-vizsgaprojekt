<?php
include_once __DIR__.'/../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: POST');
    echo $result->toJSON();
    exit();
}

$validationRules = [
    "email" => [
        "pattern" => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
        "errorMessage" => "Érvénytelen e-mail cím"
    ],
    "zipCode" => [
        "pattern" => '/^[1-9]{1}[0-9]{3}$/',
        "errorMessage" => "Érvénytelen irányítószám"
    ],
    "name" => [
        "pattern" => '/^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/',
        "errorMessage" => "Érvénytelen név"
    ],
    "phone" => [
        "pattern" => '/^(\+36|06)(\d{9})$/',
        "errorMessage" => "Érvénytelen telefonszám"
    ],
    "houseNumber" => [
        "callback" => function ($value) {
            return is_numeric($value) && $value > 0;
        },
        "errorMessage" => "Érvénytelen házszám (pozitív egész számnak kell lennie)"
    ],
    "sameAddress" => [
        "noValidate" => true,
    ],
    "purchaseTypes" => [
        "noValidate" => true,
    ],
    "taxNumber" => [
        "pattern" => '/^\d{8}-\d{1,2}-\d{2}$/',
        "errorMessage" => "Érvénytelen adószám"
    ],
];

$formFields = [
    "email" => $_POST['email'] ?? null,
    "lastName" => $_POST['last-name'] ?? null,
    "firstName" => $_POST['first-name'] ?? null,
    "phone" => $_POST['phone'] ?? null,
    "zipCode" => $_POST['zip-code'] ?? null,
    "city" => $_POST['city'] ?? null,
    "street" => $_POST['street'] ?? null,
    "houseNumber" => $_POST['house-number'] ?? null,
    "sameAddress" => isset($_POST['same-address']) ? true : false,
    "purchaseTypes" => $_POST['purchase-types'] ?? 'Magánszemélyként rendelek',
    "billingName" => $_POST['billing-name'] ?? null,
    "billingZipCode" => $_POST['billing-zip'] ?? null,
    "billingCity" => $_POST['billing-city'] ?? null,
    "billingStreet" => $_POST['billing-street'] ?? null,
    "billingHouseNumber" => $_POST['billing-house-number'] ?? null,
    "taxNumber" => $_POST['tax-number'] ?? null,
];

function validateForm($fields, $rules) {
    foreach ($fields as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];

            if (isset($rule['noValidate']) && $rule['noValidate']) {
                continue;
            }

            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                return $rule['errorMessage'];
            }

            if (isset($rule['callback']) && !$rule['callback']($value)) {
                return $rule['errorMessage'];
            }
        }
    }

    return null;
}

$error = validateForm($formFields, $validationRules);

if ($error) {
    $result = new Result(Result::ERROR, $error);
    http_response_code(400);
    echo $result->toJSON();
    exit();
}

$result = new Result(Result::SUCCESS, "Minden mező érvényes!");
echo $result->toJSON();