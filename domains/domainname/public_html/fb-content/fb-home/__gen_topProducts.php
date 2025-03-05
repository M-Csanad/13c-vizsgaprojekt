<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once BASE_PATH . '/../../../.ext/db_connect.php';
include_once BASE_PATH . '/error_logger.php';
include_once BASE_PATH . '/solid_func.php';


function generateTopProducts()
{

    try {
        $conn = db_connect();
    } catch (Exception $e) {
        logError("Adatbázis-kapcsolati hiba (topProducts): " . $e->getMessage(), 'topProducts_error.log');
        die("Nem sikerült csatlakozni az adatbázishoz. Kérlek, nézd meg a naplót.");
    }


    $sql = "SELECT id, name, unit_price, description
            FROM product
            WHERE stock > 0
            ORDER BY id
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

            $slug = slug_gen($row['name']);


            echo '<div class="swiper-slide">
                    <div class="swiper-card_wrapper">
                      <div class="card-image">
                        <img src="" alt="' . $productName . '" loading="lazy" />
                        <button class="book-now">Kosárba</button>
                      </div>
                      <div class="swiper-card">
                        <div class="text-overlay">
                          <h1 class="__t03-law1 title">' . $productName . '</h1>
                          <div class="rating">
                            <span>⭐️⭐️⭐️⭐️⭐️</span>
                            <span>180k értékelés</span>
                          </div>
                          <div class="text">
                            <p class="__t02-men1 description">' . $productDescription . '</p>
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
                              <button class="more-info">Bővebb információ</button>
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
