<?php

include_once "init.php";

function makeReview($reviewData) {
    // Forrás PHP fájl URL-jének kiszedése (termék azonosításához)
    $uri = trim(parse_url($reviewData['HTTP_REFERER'], PHP_URL_PATH), '/');
    $segments = explode('/', $uri); // 0: category, 1: subcategory, 2: product
    $slug = implode('/', $segments);

    // Termék és termékoldal adatainak lekérése
    $result = selectData("SELECT product.*, product_page.id as page_id, product_page.created_at, product_page.last_modified, product_page.page_title, product_page.page_content, category.name AS category_name, subcategory.name AS subcategory_name FROM product_page
        INNER JOIN product ON product_page.product_id=product.id
        INNER JOIN category ON product_page.category_id=category.id
        INNER JOIN subcategory ON product_page.subcategory_id=subcategory.id
        WHERE product_page.link_slug=?", $slug, "s");

    if (!$result->isSuccess()) {
        return new Result(Result::ERROR, "Ismeretlen termék.");
        exit;
    }
    $product = $result->message[0];

    // Ellenőrizzük, hogy bejelentkezett felhasználó értékelt-e.
    session_start();
    if (!isset($_SESSION["user_id"])) return new Result(Result::ERROR, "Értékelni csak bejelentkezett felhasználó tud!");

    // Ellenőrizzük, hogy a felhasználó megvásárolta-e a terméket
    $purchaseResult = selectData(
        "SELECT COUNT(*) as purchase_count
         FROM `order_item`
         JOIN `order` ON order_item.order_id = `order`.id
         WHERE `order`.user_id = ?
         AND order_item.product_id = ?
         AND `order`.status IN ('Teljesítve', 'Kiszállítva', 'Kifizetett')",
        [$_SESSION["user_id"], $product["id"]],
        "ii"
    );

    if ($purchaseResult->isError()) {
        return new Result(Result::ERROR, "Hiba a vásárlás ellenőrzése során.");
    }

    $hasPurchased = ($purchaseResult->message[0]['purchase_count'] > 0);

    // Ez a blokk megakadályozza, hogy a nem vásárlók értékelést írjanak
    if (!$hasPurchased) {
        return new Result(Result::ERROR, "Csak olyan felhasználók értékelhetnek, akik már megvásárolták, és megkapták a terméket!");
    }

    // Ellenőrizzük, hogy a felhasználó már adott-e értékelést ehhez a termékhez
    $existingReviewResult = selectData(
        "SELECT COUNT(*) as review_count FROM review WHERE user_id = ? AND product_id = ?",
        [$_SESSION["user_id"], $product["id"]],
        "ii"
    );

    if ($existingReviewResult->isError()) {
        return new Result(Result::ERROR, "Hiba az értékelés ellenőrzése során.");
    }

    $hasReviewed = ($existingReviewResult->message[0]['review_count'] > 0);

    if ($hasReviewed) {
        return new Result(Result::ERROR, "Ehhez a termékhez már adtál értékelést!");
    }

    // Értékelés beszúrása a verified_purchase zászlóval
    return updateData(
        "INSERT INTO review (user_id, product_id, rating, description, title, verified_purchase)
         VALUES (?, ?, ?, ?, ?, ?);",
        [$_SESSION["user_id"], $product["id"], doubleval($reviewData["rating"]),
         $reviewData["review-body"], $reviewData["review-title"], $hasPurchased ? 1 : 0],
        "iidssi"
    );
}

/**
 * Termék értékeléseinek lapozott lekérése
 * @param int $productId A termék azonosítója
 * @param int $page Oldalszám (1-től kezdve)
 * @param int $perPage Értékelések száma oldalanként
 * @return Result Az értékeléseket tartalmazó eredmény objektum
 */
function getReviewStats($productId) {
    // Lekérjük az értékelések számát és az átlagos értékelést
    $result = selectData(
        "SELECT COUNT(*) as review_count, AVG(rating) as average_rating
         FROM review
         WHERE product_id = ?",
        [$productId],
        "i"
    );

    if ($result->isError()) {
        return new Result(Result::ERROR, "Hiba az értékelési statisztikák lekérdezése során.");
    }

    // Visszaadjuk az értékelések számát és az átlagos értékelést
    return new Result(Result::SUCCESS, [
        'review_count' => $result->message[0]['review_count'],
        'average_rating' => round($result->message[0]['average_rating'], 2)
    ]);
}

function getProductReviews($productId, $page = 1, $perPage = 5) {
    $offset = ($page - 1) * $perPage;

    // Lapozáshoz szükséges az összes értékelés számának lekérése
    $countResult = selectData(
        "SELECT COUNT(*) as total FROM review WHERE product_id = ?",
        $productId,
        "i"
    );

    if ($countResult->isError()) {
        return new Result(Result::ERROR, "Hiba az értékelések lekérdezése során");
    }

    $totalReviews = $countResult->message[0]['total'];
    $totalPages = ceil($totalReviews / $perPage);

    $result = selectData(
        "SELECT review.id, review.rating, review.description,
                review.title, review.verified_purchase, DATE(review.created_at) AS created_at,
                user.user_name, user.first_name, user.last_name,
                avatar.uri as pfp_uri
         FROM review
         INNER JOIN user ON review.user_id = user.id
         INNER JOIN avatar ON avatar.id=user.avatar_id
         WHERE product_id=?
         ORDER BY review.created_at DESC
         LIMIT ? OFFSET ?",
        [$productId, $perPage, $offset],
        "iii"
    );

    if ($result->isError()) {
        return new Result(Result::ERROR, "Hiba az értékelések lekérdezése során");
    }

    // Értékelések visszaadása lapozási metaadatokkal
    return new Result(Result::SUCCESS, [
        'reviews' => $result->message,
        'pagination' => [
            'total' => $totalReviews,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]
    ]);
}

/**
 * Az összes értékelés számának lekérdezése
 * @return QueryResult Az értékelések számát tartalmazó eredmény objektum
 */
function getTotalReviews() {
    return selectData("SELECT COUNT(*) as total FROM review");
}