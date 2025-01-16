<?php
include_once __DIR__.'/../init.php';
include_once __DIR__.'/../router_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Hibás metódus! Várt: DELETE');
    echo $result->toJSON();
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id']) || !is_int($data['id'])) {
    http_response_code(405);
    $result = new Result(Result::ERROR, 'Üres vagy hibás paraméter');
    echo $result->toJSON();
    exit();
}
$productId = $data['id'];

session_start();

// Felhasználó adatainak lekérése
$user = null;
$isLoggedIn = false;

$result = getUserData();
if ($result->isSuccess()) {
    $isLoggedIn = true;
    $user = $result->message[0];
}

// Ha be van jelentkezve akkor a Session és az adatbázis fog változni
if ($isLoggedIn) {
    
    // Kitöröljük az adatbázisból a rekordot
    $result = updateData("DELETE FROM cart WHERE cart.user_id=? AND cart.product_id=?", [$user['id'], $productId], 'ii');
    if ($result->isError()) {
        return $result->toJSON();
    }

    // Mivel a cart_get.php bejelentkezett felhasználóknál elvégzi a többi teendőt,
    // ezért itt már nincs több dolgunk.
}
// Ha nincs bejelentkezve, akkor a Session és a Cookie fog változni
else {
    if (!isset($_SESSION['cart'])) return new Result(Result::ERROR, "Nincs kosár amiből törölni lehetne.");

    // Bejárjuk a kosarat, és ha találunk egyezést, akkor kitöröljük
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['product_id'] === $productId) {
            unset($_SESSION['cart'][$index]);
            break;
        }
    }

    // Mivel az unset után az indexek nem frissülnek, ezért ezt ki kell javítanunk
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    // Frissítjük a kosár sütit
    setCartCookie();
}