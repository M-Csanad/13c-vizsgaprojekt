<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once BASE_PATH . '/../../../.ext/db_connect.php';
include_once BASE_PATH . '/error_logger.php';
include_once BASE_PATH . '/solid_func.php';


/*
 * A legnépszerűbb termékek betöltése az adatbázisból és megjelenítése.
 * A függvény a legtöbb értékeléssel rendelkező, raktáron lévő termékeket keresi meg,
 * majd HTML formátumban jeleníti meg őket egy carousel számára.
 */
function generateTopProducts()
{

    try {
        $conn = db_connect();
    } catch (Exception $e) {
        logError("Adatbázis-kapcsolati hiba (topProducts): " . $e->getMessage(), 'topProducts_error.log');
        die("Nem sikerült csatlakozni az adatbázishoz. Kérlek, nézd meg a naplót.");
    }


    $sql = "SELECT p.id, p.name, p.unit_price, p.description, p.stock,
                MAX(i.uri) AS image_uri,
                COALESCE(AVG(r.rating), 0) as avg_rating,
                COUNT(DISTINCT r.id) as review_count
            FROM product p
            LEFT JOIN product_image pi ON p.id = pi.product_id
            LEFT JOIN image i ON pi.image_id = i.id
            LEFT JOIN review r ON p.id = r.product_id
            WHERE p.stock > 0
            GROUP BY p.id, p.name, p.unit_price, p.description, p.stock
            ORDER BY review_count DESC, avg_rating DESC
            LIMIT 10";

    try {
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("SQL hiba: " . $conn->error);
        }
    } catch (Exception $e) {
        logError("SQL végrehajtási hiba (topProducts): " . $e->getMessage(), 'topProducts_error.log');
        die("SQL hiba történt. Kérlek, nézd meg a naplót.");
    }


    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productId = $row['id'];
            $productName = htmlspecialchars($row['name']);
            $productDescription = htmlspecialchars($row['description']);
            $avgRating = $row['avg_rating'];
            $reviewCount = $row['review_count'];
            $reviewText = $reviewCount > 0 ? "{$reviewCount} értékelés" : "Még nincs értékelve";
            $stock = (int)$row['stock'];
            $imageUri = $row['image_uri'];

            $pageQuery = "SELECT link_slug FROM product_page WHERE product_id = ?";
            $stmt = $conn->prepare($pageQuery);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $pageResult = $stmt->get_result();
            $productUrl = "";

            if ($pageResult && $pageResult->num_rows > 0) {
                $page = $pageResult->fetch_assoc();
                $productUrl = "/" . $page['link_slug'];
            }

            $stmt->close();

            $basePath = preg_replace('/\.[^.]+$/', '', $imageUri);

            echo '<div class="swiper-slide">
                    <div class="swiper-card_wrapper">
                      <div class="card-image">';

            if ($imageUri) {
                echo '<img src="' . $imageUri . '" alt="' . $productName . '" loading="lazy" />';
            } else {
                echo '<img src="/fb-content/assets/media/images/site/no_image.jpg" alt="' . $productName . '" loading="lazy" />';
            }

            echo '    <button class="quick-add" data-product-id="' . $productId . '" data-product-url="' . $productUrl . '">Kosárba</button>
                      </div>
                      <div class="swiper-card">
                        <div class="text-overlay">
                          <h1 class="__t03-law1 title">' . $productName . '</h1>
                          <div class="rating">
                            <div class="review-stars stars" data-rating="' . number_format($avgRating, 1) . '"></div>
                            <span>' . $reviewText . '</span>
                          </div>
                          <div class="text">
                            <p class="__t02-men1 description">'. implode(' ', array_slice(explode(' ', htmlspecialchars($productDescription)), 0, 20));
              echo '...</p>
                          </div>
                          <div class="card-footer">
                            <div class="cast">';


            $sql_tags = "SELECT t.name, t.icon_uri
                         FROM product_tag pt
                         JOIN tag t ON pt.tag_id = t.id
                         WHERE pt.product_id = " . intval($productId);

            $result_tags = $conn->query($sql_tags);
            if ($result_tags && $result_tags->num_rows > 0) {
                while ($tag = $result_tags->fetch_assoc()) {
                    $tagName = htmlspecialchars($tag['name']);
                    $iconURI = htmlspecialchars($tag['icon_uri']);
                    echo '<img src="' . $iconURI . '" alt="' . $tagName . '" loading="lazy" />';
                }
            }


            echo '         </div>
                            <div class="actions">
                              <a href="' . $productUrl . '" class="more-info">Bővebb információ</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>';
        }
    } else {

        echo '<p>Nincsenek elérhető termékek.</p>';
    }


    db_disconnect($conn);
}


generateTopProducts();
