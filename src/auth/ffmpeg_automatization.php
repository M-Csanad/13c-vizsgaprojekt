<?php

require_once 'error_logger.php';

/**
 * Kép optimalizálása több méretre és formátumra.
 * Párhuzamos feldolgozást alkalmaz.
 *
 * @param string $inputFile A bemeneti kép elérési útja.
 * @param array $sizes A kívánt méretek (szélességek).
 * @param int $quality A tömörítés minősége (0-100). (alapértelmezett 40) 30-40 között az optimális!
 * @param int $maxRetries Maximális újrapróbálási szám (alapértelmezett 3). // 0-nál nagyobb értéket minimum meg kell adni
 * @return void
 */
function optimizeImage($inputFile, $sizes = [3840, 2560, 1920, 1440, 1024, 768], $quality = 40, $maxRetries = 3)
{
    // Ellenőrizzük, hogy a bemeneti fájl létezik-e
    if (!file_exists($inputFile)) {
        logError("A megadott fájl nem található: $inputFile", "image_optimization.log");
        return;
    }

    // FFMPEG elérési útjának beállítása
    $ffmpegPath = 'C:/xampp/htdocs/13c-vizsgaprojekt/bin/ffmpeg/bin/ffmpeg.exe';
    if (!is_executable($ffmpegPath)) {
        logError("Az FFMPEG nem elérhető: $ffmpegPath", "image_optimization.log");
        return;
    }

    // Fájl információk kinyerése
    $fileInfo = pathinfo($inputFile);
    $dirname = $fileInfo['dirname'];
    $filename = $fileInfo['filename'];

    // Párhuzamos folyamatok tárolása
    $processes = [];

    // Végigmegyünk a kívánt méreteken
    foreach ($sizes as $width) {
        // Kimeneti fájlok elérési útjai
        $outputWebP = "$dirname/{$filename}-{$width}px.webp";
        $outputAVIF = "$dirname/{$filename}-{$width}px.avif";

        // FFMPEG parancsok
        $commandWebP = escapeshellcmd("$ffmpegPath -i " . escapeshellarg($inputFile) . " -vf scale={$width}:-1 -c:v libwebp -qscale:v $quality -threads 4 -y " . escapeshellarg($outputWebP));
        $commandAVIF = escapeshellcmd("$ffmpegPath -i " . escapeshellarg($inputFile) . " -vf scale={$width}:-1 -c:v libaom-av1 -crf $quality -b:v 0 -threads 4 -y " . escapeshellarg($outputAVIF));

        // Indítjuk a WebP konvertálást külön folyamatban
        $processes[] = [
            'command' => $commandWebP,
            'outputFile' => $outputWebP,
            'format' => 'WebP',
            'width' => $width,
        ];

        // Indítjuk az AVIF konvertálást külön folyamatban
        $processes[] = [
            'command' => $commandAVIF,
            'outputFile' => $outputAVIF,
            'format' => 'AVIF',
            'width' => $width,
        ];
    }

    // Folyamatok kezelése
    foreach ($processes as $process) {
        $retryCount = 0;
        while ($retryCount < $maxRetries) {
            shell_exec($process['command']);

            // Ellenőrizzük, hogy a kimeneti fájl sikeresen létrejött-e és nem üres
            if (file_exists($process['outputFile']) && filesize($process['outputFile']) > 0) {
                logError("Sikeres konvertálás: {$process['format']} ({$process['width']}px)", "image_optimization.log");
                break;
            } else {
                logError("Nem sikerült a {$process['format']} konvertálás: {$process['outputFile']}. Újrapróbálás ({$retryCount})", "image_optimization.log");
                if (file_exists($process['outputFile'])) {
                    unlink($process['outputFile']); // Hibás fájl törlése
                }
                $retryCount++;
            }

            // Ha a maximális újrapróbálási számot elértük és még mindig sikertelen
            if ($retryCount == $maxRetries) {
                logError("Végleges hiba a {$process['format']} konvertálás során: {$process['outputFile']}", "image_optimization.log");
                convertWithGD($inputFile, $process['width'], $process['outputFile']);
            }
        }
    }
}

/**
 * Kép átméretezése GD-vel.
 *
 * @param string $inputFile Az eredeti kép elérési útja.
 * @param int $width A kívánt szélesség.
 * @param string $outputFile A kimeneti fájl elérési útja.
 * @return void
 */
function convertWithGD($inputFile, $width, $outputFile)
{
    $image = @imagecreatefromjpeg($inputFile);
    if (!$image) {
        logError("GD nem tudta betölteni a képet: $inputFile", "image_optimization.log");
        return;
    }

    $origWidth = imagesx($image);
    $origHeight = imagesy($image);
    $newHeight = intval($origHeight * ($width / $origWidth));

    $resizedImage = imagecreatetruecolor($width, $newHeight);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $newHeight, $origWidth, $origHeight);

    // Mentés WebP formátumban
    if (str_ends_with($outputFile, '.webp')) {
        imagewebp($resizedImage, $outputFile);
    }
    // Mentés AVIF formátumban (GD nem támogatja natívan AVIF-et, így itt a fallback a WebP lesz)
    else {
        imagewebp($resizedImage, $outputFile);
    }

    logError("GD sikeresen mentette a képet: $outputFile", "image_optimization.log");

    // Erőforrások felszabadítása
    imagedestroy($image);
    imagedestroy($resizedImage);
}

// Használat - teszt
try {
    optimizeImage('C:/xampp/htdocs/13c-vizsgaprojekt/src/images/categories/TEST_FOLDER/thumbnail_image_horizontal.jpg');
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage(), "image_optimization.log");
}
?>

