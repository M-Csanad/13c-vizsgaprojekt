<?php

/**
 * Szöveg formázása URL-barát slug formátumra.
 *
 * Ez a függvény egy megadott szöveget URL-barát "slug"-gá alakít.
 * - Ékezetes karaktereket helyettesít nem ékezetes megfelelőikkel.
 * - Minden szöveget kisbetűsít.
 * - Speciális karaktereket eltávolít.
 * - Szóközöket kötőjelekké alakít.
 * - Több egymást követő kötőjelet egyetlen kötőjellé egyszerűsít.
 * - Eltávolítja a kötőjeleket a szöveg elejéről és végéről.
 *
 * @param string $s A bemeneti szöveg, amit slug formátumra kell alakítani.
 * @return string A formázott, URL-barát slug.
 *
 * Példák:
 * slug_gen("A Tiszta Egészség!"); // Eredmény: "a-tiszta-egeszseg"
 * slug_gen("  Hé, Miért Van   Ennyi !!!  "); // Eredmény: "he-miert-van-ennyi"
 * slug_gen("Hosszú Ű Ö Á Í"); // Eredmény: "hosszu-u-o-a-i"
 */
function slug_gen($s)
{
    $hungarian_to_english = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'Á' => 'a',
        'É' => 'e',
        'Í' => 'i',
        'Ó' => 'o',
        'Ö' => 'o',
        'Ő' => 'o',
        'Ú' => 'u',
        'Ü' => 'u',
        'Ű' => 'u'
    ];

    $s = strtr(mb_strtolower($s), $hungarian_to_english);
    $s = preg_replace('/[^a-z0-9 -]/', '', $s);
    $s = str_replace(' ', '-', $s);
    $s = preg_replace('/-+/', '-', $s);

    return trim($s, '-');
}


?>

