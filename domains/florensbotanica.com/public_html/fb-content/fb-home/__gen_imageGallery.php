<?php
include_once __DIR__ . '../../../../../.ext/db_connect.php';

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
      $fileInfo_horizontal = pathinfo(htmlspecialchars($row['thumbnail_image_horizontal_uri']));
      $FileName_horizontal = $fileInfo_horizontal['dirname'] . '/' . $fileInfo_horizontal['filename'];
      $resolutions_horizontal = array(3840, 2560, 1920, 1440, 1024, 768);

      echo '<div class="media_box image">
                    <picture>';

      // Horizontal képek forrásai (AVIF, WebP, JPG)
      foreach ($resolutions_horizontal as $resolution) {
        echo '<source type="image/avif" media="(min-width:' . $resolution . 'px)" srcset="../assets/media/' . $FileName_horizontal . '-' . $resolution . 'px.avif">';
        echo '<source type="image/webp" media="(min-width:' . $resolution . 'px)" srcset="../assets/media/' . $FileName_horizontal . '-' . $resolution . 'px.webp">';
        echo '<source type="image/jpeg" media="(min-width:' . $resolution . 'px)" srcset="../assets/media/' . $FileName_horizontal . '-' . $resolution . 'px.jpg">';
      }

      // Fallback kép
      echo '<img src="../assets/media/' . $FileName_horizontal . '-' . $resolutions_horizontal[2] . 'px.jpg"
                      alt="' . htmlspecialchars($row['name']) . ' ' . htmlspecialchars($row['subname']) . '"
                      loading="lazy">';

      echo '</picture>
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

