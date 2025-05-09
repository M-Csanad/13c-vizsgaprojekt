<?php

$table = $_POST['table'] ?? '';

if ($table) {
    include_once __DIR__."/../init.php";

    $matches = '';
    if ($table == 'category') {
        $matches = selectData("SELECT category.id, category.name FROM category;");
    }
    else {
        if (!isset($_POST['category_name'])) {
            echo "Nincs főkategória kiválasztva!";
            return;
        }

        $matches = selectData("SELECT subcategory.id, subcategory.name FROM subcategory 
                               INNER JOIN category ON subcategory.category_id = category.id
                               WHERE category.name = ?;", $_POST['category_name'], "s");
    }
    echo $matches->toJSON();
}