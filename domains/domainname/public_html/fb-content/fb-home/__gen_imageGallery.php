<?php include_once 'config.php';
include_once BASE_PATH . '/../../../.ext/db_connect.php';
include_once BASE_PATH . '/error_logger.php'; // Naplózás integrálása
include_once BASE_PATH . '/solid_func.php';

/**
 * Galéria képeket generál az adatbázis kategóriái alapján.
 */
function generateImageGallery()
{
    // Adatbázis-kapcsolat létrehozása
    try {
        $conn = db_connect();
    } catch (Exception $e) {
        logError("Adatbázis-kapcsolati hiba: " . $e->getMessage(), 'imageGallery_error.log');
        die("Nem sikerült csatlakozni az adatbázishoz. Kérlek, nézd meg a naplót.");
    }

    // SQL lekérdezés
    $sql = "SELECT category.name, category.subname, category.thumbnail_image_horizontal_uri, category.product_count
            FROM category";

    try {
        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception("SQL hiba: " . $conn->error);
        }
    } catch (Exception $e) {
        logError("SQL végrehajtási hiba: " . $e->getMessage(), 'imageGallery_error.log');
        die("SQL hiba történt. Kérlek, nézd meg a naplót.");
    }

    // Eredmények feldolgozása
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Slug generálása a kategória nevéből
            $slug = format_str($row['name']);

            $fileInfo_horizontal = pathinfo(htmlspecialchars($row['thumbnail_image_horizontal_uri']));
            $FileName_horizontal = $fileInfo_horizontal['dirname'] . '/' . $fileInfo_horizontal['filename'];
            $resolutions_horizontal = [3840, 2560, 1920, 1440, 1024, 768];

            echo '<div class="media_box image">
                    <picture>';

            // Horizontal képek forrásai (AVIF, WebP, JPG)
            foreach ($resolutions_horizontal as $resolution) {
                echo '<source type="image/avif" media="(min-width:' . $resolution . 'px)" srcset="' . $FileName_horizontal . '-' . $resolution . 'px.avif">';
                echo '<source type="image/webp" media="(min-width:' . $resolution . 'px)" srcset="' . $FileName_horizontal . '-' . $resolution . 'px.webp">';
                echo '<source type="image/jpeg" media="(min-width:' . $resolution . 'px)" srcset="' . $FileName_horizontal . '-' . $resolution . 'px.jpg">';
            }

            // Fallback kép
            echo '<img src="' . $FileName_horizontal . '-' . $resolutions_horizontal[2] . 'px.jpg"
                      alt="' . htmlspecialchars($row['name']) . ' ' . htmlspecialchars($row['subname']) . '"
                      loading="lazy">';

            echo '</picture>
                    <div class="caption">
                      <p class="__t03-law5 title_caption">
                        <a href="http://localhost/' . htmlspecialchars($slug) . '">'
                . htmlspecialchars($row['name']) .
                '</a>
                      </p>
                      <div class="subcontent_caption">
                        <p class="__t01-mew1 subtitle_caption">' . htmlspecialchars($row['subname']) . '</p>
                        <p class="__t02-mew1 productCount_caption">' . htmlspecialchars($row['product_count']) . ' termék</p>
                      </div>
                    </div>
                  </div>';
        }
    } else {
        logError("Nincsenek elérhető kategóriák a galériához.", 'imageGallery_error.log');
        echo '<p>Nincsenek elérhető kategóriák a galériához.</p>';
    }

    // Adatbázis-kapcsolat lezárása
    db_disconnect($conn);

}

// Galéria generálása
generateImageGallery();
?>

