<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/13c-vizsgaprojekt/src/auth/db_connect.php';

function generateImageGallery()
{
    $conn = createConnection();

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $sql = "SELECT category.name, category.subname, category.thumbnail_image_horizontal_uri FROM category";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {


        while ($row = $result->fetch_assoc()) {
            echo '<div class="media_box image">
                    <img src="../' . htmlspecialchars($row['thumbnail_image_horizontal_uri']) . '" alt="' . htmlspecialchars($row['name']) . '" />
                    <div class="caption">
                      <p class="__t03-law5 title_caption">' . htmlspecialchars($row['name']) . '</p>
                      <div class="subcontent_caption">
                        <p class="__t01-mew1 subtitle_caption">' . htmlspecialchars($row['subname']) . '</p>
                        <p class="__t02-mew1 productCount_caption">xyz termék</p>
                      </div>
                    </div>
                  </div>';
        }


    } else {
        echo '<p>Nincsenek elérhető kategóriák a galériához.</p>';
    }

    $conn->close();
}

generateImageGallery();
?>

