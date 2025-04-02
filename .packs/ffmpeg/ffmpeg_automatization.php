<?php

require_once 'error_logger.php';

/**
 * Kép optimalizálása több méretre és formátumra.
 * Párhuzamos feldolgozást alkalmaz.
 *
 * @param string $inputFile A bemeneti kép elérési útja.
 * @param array $sizes A kívánt méretek (szélességek).
 * @param int $quality A tömörítés minősége (0-100). minnél kissebb, annál nagyobb a TÖMÖRÍTÉS
 * @param int $maxRetries Maximális újrapróbálási szám (alapértelmezett 3).
 * @return void
 */
function optimizeImage($inputFile, $sizes = [1920, 1440, 1024, 768], $quality = 65, $maxRetries = 3)
{
    // Ellenőrizzük, hogy a bemeneti fájl létezik-e
    if (!file_exists($inputFile)) {
        logError("A megadott fájl nem található: $inputFile", "image_optimization.log");
        return;
    }

    // FFMPEG elérési útjának beállítása
    $ffmpegPath = $_SERVER["DOCUMENT_ROOT"] . '/../../../.packs/ffmpeg/bin/ffmpeg/bin/ffmpeg.exe';
    if (!is_executable($ffmpegPath)) {
        logError("Az FFMPEG nem elérhető: $ffmpegPath", "image_optimization.log");
        return;
    }

    ini_set('max_execution_time', 600);

    // Fájl információk kinyerése
    $fileInfo = pathinfo($inputFile);
    $dirname = $fileInfo['dirname'];
    $filename = $fileInfo['filename'];

    // Párhuzamos folyamatok tárolása
    $processes = [];

    // Alapértelmezett minőségek formátumokhoz igazítva - AZ ÉRTÉK FONTOS, TILOS VÁLTOZTATNI
    $qualityMapping = [
        'WebP' => min(max($quality, 0), 100), // Egyenes skála: 0-100 - 0 jó tömörítés, 100 jó minőség
        'AVIF' => min(max(63 - floor($quality * (63 / 100)), 0), 63),  // Fordított skála: 0-63 - 0 jó minőség, 63 jó tömörítés
        'JPG' => min(max(31 - floor($quality / 3), 2), 31) // Fordított skála: 2 jó minőség, 31 jó tömörítés
    ];

    // Eredeti méretű fájlok generálása WebP és AVIF formátumban
    $originalWebP = "$dirname/{$filename}.webp";
    $originalAVIF = "$dirname/{$filename}.avif";

    // FFMPEG parancsok az EREDETI méretű fájlokhoz
    $commandOriginalWebP = escapeshellcmd(
        "$ffmpegPath -i " . escapeshellarg($inputFile) .
        " -c:v libwebp -qscale:v {$qualityMapping['WebP']} -threads 4 -y " .
        escapeshellarg($originalWebP)
    );

    $commandOriginalAVIF = escapeshellcmd(
        "$ffmpegPath -i " . escapeshellarg($inputFile) .
        " -c:v libaom-av1 -crf {$qualityMapping['AVIF']} -b:v 0 -threads 4 -y " .
        escapeshellarg($originalAVIF)
    );

    // Csak akkor adjuk hozzá a konvertálandó folyamatokhoz,
    // ha nem létezik vagy 0 bájtos a fájl.
    if (!file_exists($originalWebP) || filesize($originalWebP) === 0) {
        $processes[] = [
            'command' => $commandOriginalWebP,
            'outputFile' => $originalWebP,
            'format' => 'WebP',
            'width' => 'original',
        ];
    }

    if (!file_exists($originalAVIF) || filesize($originalAVIF) === 0) {
        $processes[] = [
            'command' => $commandOriginalAVIF,
            'outputFile' => $originalAVIF,
            'format' => 'AVIF',
            'width' => 'original',
        ];
    }

    // Végigmegyünk a kívánt méreteken
    foreach ($sizes as $width) {
        // Kimeneti fájlok elérési útjai
        $outputWebP = "$dirname/{$filename}-{$width}px.webp";
        $outputAVIF = "$dirname/{$filename}-{$width}px.avif";
        $outputJPG = "$dirname/{$filename}-{$width}px.jpg";


        // FFMPEG parancsok
        $commandWebP = escapeshellcmd(
            "$ffmpegPath -i " . escapeshellarg($inputFile) .
            " -vf scale={$width}:-1 -c:v libwebp -qscale:v {$qualityMapping['WebP']} -threads 4 -y " .
            escapeshellarg($outputWebP)
        );

        $commandAVIF = escapeshellcmd(
            "$ffmpegPath -i " . escapeshellarg($inputFile) .
            " -vf scale={$width}:-1 -c:v libaom-av1 -crf {$qualityMapping['AVIF']} -b:v 0 -threads 4 -y " .
            escapeshellarg($outputAVIF)
        );

        $commandJPG = escapeshellcmd(
            "$ffmpegPath -i " . escapeshellarg($inputFile) .
            " -vf scale={$width}:-1 -qscale:v {$qualityMapping['JPG']} -threads 4 -y " .
            escapeshellarg($outputJPG)
        );

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

        // Indítjuk a JPG konvertálást külön folyamatban
        $processes[] = [
            'command' => $commandJPG,
            'outputFile' => $outputJPG,
            'format' => 'JPG',
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
                // Fallback konvertálás GD-vel
                convertWithGD($inputFile, $process['width'], $process['outputFile'], $quality);
            }
        }
    }
}




/**
 * Kép átméretezése GD-vel (fallback).
 *
 * @param string $inputFile Az eredeti kép elérési útja.
 * @param int $width A kívánt szélesség.
 * @param string $outputFile A kimeneti fájl elérési útja.
 * @param int $quality Minőség (0-100) - alapértelmezetten 60. 10-30: Nagyon tömörített, észrevehetően rosszabb minőség. 50-70: Közepes minőség, kis fájlmérettel. 80-100: Nagyon jó minőség, nagyobb fájlmérettel.
 * @return void
 */
function convertWithGD($inputFile, $width, $outputFile, $quality = 60)
{
    $image = imagecreatefromjpeg($inputFile);
    if (!$image) {
        logError("GD nem tudta betölteni a képet: $inputFile", "image_optimization.log");
        return;
    }

    $width = intval($width);

    $origWidth = imagesx($image);
    $origHeight = imagesy($image);
    $newHeight = intval($origHeight * ($width / $origWidth));

    $resizedImage = imagecreatetruecolor($width, $newHeight);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $newHeight, $origWidth, $origHeight);

    $outputExt = strtolower(pathinfo($outputFile, PATHINFO_EXTENSION));

    switch ($outputExt) {
        case 'webp':
            imagewebp($resizedImage, $outputFile, $quality);
            break;

        case 'avif':
            // Natív AVIF támogatás hiányában fallback WebP formátum mentés
            imagewebp($resizedImage, $outputFile, $quality);
            break;

        case 'jpg':
        case 'jpeg':
            imagejpeg($resizedImage, $outputFile, $quality);
            break;

        default:
            imagewebp($resizedImage, $outputFile, $quality);
            break;
    }

    logError("GD sikeresen mentette a képet: $outputFile", "image_optimization.log");

    // Erőforrások felszabadítása
    imagedestroy($image);
    imagedestroy($resizedImage);
}
