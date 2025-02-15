<?php
include_once __DIR__.'/../result_functions.php';
include_once __DIR__.'/../order_functions.php';
include_once __DIR__.'/../cart_functions.php';

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
    "streetHouse" => [
        "pattern" => '/^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+(?: [A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+)? [a-záéíóöőúüű]{2,} \d{1,}(\.?|(?:\/[A-Z]+(?: \d+\/\d+)?))$/',
        "errorMessage" => "Érvénytelen házszám (pozitív egész számnak kell lennie)"
    ],
    "sameAddress" => [
        "noValidate" => true,
    ],
    "purchaseType" => [
        "noValidate" => true,
    ],
    "companyName" => [
        "callback" => function ($value) {
            return mb_strlen($value) > 0;
        },
        "errorMessage" => "Érvénytelen cégnév"
    ],
    "taxNumber" => [
        "pattern" => '/^\d{8}-\d{1,2}-\d{2}$/',
        "errorMessage" => "Érvénytelen adószám"
    ],
];

$formFields = [
    "company" => [
        "companyName" => $_POST['company-name'] ?? null,
        "taxNumber" => $_POST['tax-number'] ?? null,
    ],
    "purchaseType" => $_POST['purchase-type'] ?? "Magánszemélyként rendelek",
    "customer" => [
        "email" => $_POST['email'] ?? null,
        "lastName" => $_POST['last-name'] ?? null,
        "firstName" => $_POST['first-name'] ?? null,
        "phone" => $_POST['phone'] ?? null,
    ],
    "delivery" => [
        "zipCode" => $_POST['zip-code'] ?? null,
        "city" => $_POST['city'] ?? null,
        "streetHouse" => $_POST['street-house'] ?? null,
    ],
    "billing" => [
        "sameAddress" => $_POST['same-address'] === "true" ? true : false,
        "zipCode" => $_POST['billing-zip'] ?? null,
        "city" => $_POST['billing-city'] ?? null,
        "streetHouse" => $_POST['billing-street-house'] ?? null,
    ]
];

function validateForm($fields, $rules) {
    foreach ($fields as $section => $sectionFields) {

        // Ha ugyanaz a számlázási cím mint a szállítási, akkor nem kell validálni a számlázásit.
        if ($section === "billing" && $fields['billing']['sameAddress']) {
            continue;
        }

        if ($section === "company" && $fields['purchaseType'] === "Magánszemélyként rendelek") {
            continue;
        }

        // Ha nem tömb, akkor csak egy elemű a szekció
        if (!is_array($sectionFields)) {
            $rule = $rules[$section];

            if (isset($rule['noValidate']) && $rule['noValidate']) {
                continue;
            }

            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $sectionFields)) {
                return $rule['errorMessage'] . " ($section)";
            }

            if (isset($rule['callback']) && !$rule['callback']($sectionFields)) {
                return $rule['errorMessage'] . " ($section)";
            }
        }

        foreach ($sectionFields as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];

                if (isset($rule['noValidate']) && $rule['noValidate']) {
                    continue;
                }

                if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                    return $rule['errorMessage'] . " ($section - $key)";
                }

                if (isset($rule['callback']) && !$rule['callback']($value)) {
                    return $rule['errorMessage'] . " ($section - $key)";
                }
            }
        }
    }

    return null;
}

// Értékek validálása
$error = validateForm($formFields, $validationRules);

if ($error) {
    $result = new Result(Result::ERROR, $error);
    http_response_code(400);
    echo $result->toJSON();
    exit();
}

// Rendelés leadása
$result = newOrder($formFields);
if (!$result->isSuccess()) {
    http_response_code(400);
    echo $result->toJSON(true);
    exit();
}

$result = clearCart();
if (!$result->isSuccess()) {
    http_response_code(400);
    echo $result->toJSON();
    exit();
}

echo (new Result(Result::SUCCESS, "Sikeres rendelés."))->toJSON();