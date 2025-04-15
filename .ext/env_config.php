<?php

include_once __DIR__ . '/errorlog.php'; // Logger funkció

/**
 * Betölti a .env fájlt és beállítja a környezeti változókat, szükség esetén hibákat naplózva.
 *
 * @param string $path A .env fájl elérési útja.
 * @return void
 * @throws Exception Ha a .env fájl nem létezik vagy nem tölthető be.
 */
if (!function_exists('load_env')) {
    function load_env($path)
    {
        if (!file_exists($path)) {
            log_Error("A .env fájl nem létezik ezen az elérési úton: $path", "env_error.log");
            throw new Exception("A .env fájl nem létezik ezen az elérési úton: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Hozzászólások kihagyása
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // A sor felosztása névre és értékre
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $name = trim($parts[0]);
                $value = trim($parts[1]);

                // Hozzáadás a $_ENV és $_SERVER tömbökhöz, ha még nincs beállítva
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }

                if (!array_key_exists($name, $_SERVER)) {
                    $_SERVER[$name] = $value;
                }
            } else {
                log_Error("Érvénytelen sor a .env fájlban: $line", "env_error.log");
            }
        }
    }
}

?>
