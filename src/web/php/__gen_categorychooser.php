<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/13c-vizsgaprojekt/src/auth/db_connect.php';

function generateHTML()
{
    $conn = createConnection();

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $sql = "SELECT category.name, category.subname, category.description, category.thumbnail_image_vertical_uri, category.thumbnail_image_horizontal_uri FROM category";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Swiper fő tartalom
        echo '<div class="swiper bg_slider"><div class="swiper-wrapper">';

        while ($row = $result->fetch_assoc()) {
            $name_parts = explode(' ', $row['name'], 2);
            $first_word = $name_parts[0];
            $remaining_words = isset($name_parts[1]) ? $name_parts[1] : '';

            echo '<div class="swiper-slide">
                    <div class="img-wrapper">
                        <div class="content_wrapper">
                            <div class="text-main">
                                <h2 class="__t00-law5-custom02">' . htmlspecialchars($first_word) . '</h2>
                                <h1 class="__t00-law5-custom01">' . htmlspecialchars($remaining_words) . '</h1>
                                <div class="underline"></div>
                            </div>
                            <div class="text-overlay">
                                <h3 class="__t03-law5">' . htmlspecialchars($row['subname']) . '</h3>
                                <p class="__t02-men1">' . htmlspecialchars($row['description']) . '</p>
                            </div>
                        </div>
                        <picture>
                            <source media="(min-width: 320px) and (orientation: portrait)" srcset="../' . htmlspecialchars($row['thumbnail_image_vertical_uri']) . '">
                            <source media="(max-width: 1024px)" srcset="../' . htmlspecialchars($row['thumbnail_image_vertical_uri']) . '">
                            <img src="../' . htmlspecialchars($row['thumbnail_image_horizontal_uri']) . '" alt="">
                        </picture>
                    </div>
                    <div class="swiper-button-next frosted-glass"></div>
                    <div class="swiper-button-prev frosted-glass"></div>
                </div>';
        }

        echo '</div></div>';

        // Swiper thumbnail tartalom
        echo '<div class="swiper bg_slider-thumbs"><div class="swiper-wrapper thumbs-container">';

        // Újraindítjuk az eredményhalmaz mutatóját
        $result->data_seek(0);

        while ($row = $result->fetch_assoc()) {
            $name_parts = explode(' ', $row['name'], 2);
            $remaining_words = isset($name_parts[1]) ? $name_parts[1] : '';

            echo '<div class="swiper-slide">
                    <div class="circleImg">
                        <img src="../' . htmlspecialchars($row['thumbnail_image_horizontal_uri']) . '" alt="' . htmlspecialchars($row['name']) . '" />
                        <div class="halo">
                            <div class="point"></div>
                        </div>
                    </div>
                    <div class="contentImg">
                        <p class="__t01-men1">' . htmlspecialchars($row['name']) . '</p>
                        <h2 class="__t03-law4">' . htmlspecialchars($row['subname']) . '</h2>
                    </div>
                </div>';
        }

        echo '</div><div><input type="range" class="thumb-slider" min="0" max="100" value="0" /></div></div>';
        echo '<div class="thumb-slider_tooltip" id="thumb-slider_tooltip"><< Húzz meg>></div>';
    } else {
        echo "Nincsenek elérhető kategóriák.";
    }

    $conn->close();
}

generateHTML();
?>

