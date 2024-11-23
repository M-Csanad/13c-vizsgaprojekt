<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode("Hibás metódus! Várt: GET, Aktuális: ".$_SERVER["REQUEST_METHOD"], JSON_UNESCAPED_UNICODE);
    header("bad request", true, 400);
    return;
}

if (!isset($_POST["search_term"]) || empty($_POST["search_term"])) {
    header("bad request", true, 400);
    echo json_encode("Hiányos adat!", JSON_UNESCAPED_UNICODE);
    return;
}

include "../auth/init.php";

$searchTerm = $_POST['search_term'];
$searchTerm = "%".$searchTerm."%";

$matches = selectData("SELECT product_page.* FROM product_page 
                        INNER JOIN product ON product_page.product_id=product.id
                        WHERE product.name LIKE ? 
                        OR product_page.page_title LIKE ? 
                        OR product_page.link_slug LIKE ? 
                        ORDER BY product_page.page_title;", 
                        array_fill(0, 3, $searchTerm));

echo json_encode($matches, JSON_UNESCAPED_UNICODE);

?>