<?php

include_once __DIR__ . '/env_config.php';

/**
 * Adatbázis kapcsolat létrehozása környezeti változók használatával, naplózással.
 *
 * @return mysqli Az adatbázis kapcsolat objektuma.
 * @throws Exception Ha a kapcsolat sikertelen.
 */
if (!function_exists('db_connect')) {
    function db_connect()
    {
        // Környezeti változók betöltése
        try {
            load_env(__DIR__ . '/.env');
        } catch (Exception $e) {
            log_Error("Nem sikerült betölteni a környezeti változókat: " . $e->getMessage(), "db_error.log");
            throw $e;
        }

        $servername = $_ENV['DB_SERVERNAME'] ?? null;
        $username = $_ENV['DB_MAIN_USERNAME'] ?? null;
        $password = $_ENV['DB_MAIN_PASSWORD'] ?? null;
        $dbname = $_ENV['DB_MAIN_NAME'] ?? null;

        if (!$servername || !$username || !$dbname) {
            log_Error("Hiányzó szükséges adatbázis konfiguráció a .env fájlban", "db_error.log");
            throw new Exception("Hiányzó szükséges adatbázis konfiguráció a .env fájlban");
        }

        try {
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Kapcsolat ellenőrzése
            if ($conn->connect_error) {
                log_Error("Adatbázis kapcsolat sikertelen: " . $conn->connect_error, "db_error.log");
                throw new Exception("Kapcsolat sikertelen: " . $conn->connect_error);
            }

            // Karakterkészlet beállítása
            if (!$conn->set_charset("utf8mb4")) {
                log_Error("Nem sikerült beállítani a karakterkészletet: " . $conn->error, "db_error.log");
            }

            return $conn;
        } catch (mysqli_sql_exception $e) {
            log_Error("SQL kivétel a kapcsolat során: " . $e->getMessage(), "db_error.log");
            throw new Exception("Adatbázis kapcsolat hiba: " . $e->getMessage());
        }
    }
}

/**
 * Az adott adatbázis kapcsolat lezárása, szükség esetén naplózással.
 *
 * @param mysqli $conn Az adatbázis kapcsolat, amelyet le kell zárni.
 * @return void
 */
if (!function_exists('db_disconnect')) {
    function db_disconnect($conn)
    {
        if ($conn instanceof mysqli) {
            $conn->close();
        } else {
            log_Error("Nem mysqli objektumot próbáltak lezárni.", "db_error.log");
        }
    }
}

?>
