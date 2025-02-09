<?php

include_once __DIR__ . '/env_config.php';

/**
 * Establishes a database connection using environment variables, with logging.
 *
 * @return mysqli The database connection object.
 * @throws Exception If the connection fails.
 */
if (!function_exists('db_connect')) {
    function db_connect()
    {
        // Load environment variables
        try {
            load_env(__DIR__ . '/.env');
        } catch (Exception $e) {
            log_Error("Failed to load environment variables: " . $e->getMessage(), "db_error.log");
            throw $e;
        }

        $servername = $_ENV['DB_SERVERNAME'] ?? null;
        $username = $_ENV['DB_MAIN_USERNAME'] ?? null;
        $password = $_ENV['DB_MAIN_PASSWORD'] ?? null;
        $dbname = $_ENV['DB_MAIN_NAME'] ?? null;

        if (!$servername || !$username || !$dbname) {
            log_Error("Missing required database configuration in .env", "db_error.log");
            throw new Exception("Missing required database configuration in .env");
        }

        try {
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                log_Error("Database connection failed: " . $conn->connect_error, "db_error.log");
                throw new Exception("Connection failed: " . $conn->connect_error);
            }

            // Set character set
            if (!$conn->set_charset("utf8mb4")) {
                log_Error("Failed to set character set: " . $conn->error, "db_error.log");
            }

            return $conn;
        } catch (mysqli_sql_exception $e) {
            log_Error("SQL exception during connection: " . $e->getMessage(), "db_error.log");
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }
}

/**
 * Closes the given database connection, with logging if needed.
 *
 * @param mysqli $conn The database connection to close.
 * @return void
 */
if (!function_exists('db_disconnect')) {
    function db_disconnect($conn)
    {
        if ($conn instanceof mysqli) {
            $conn->close();
        } else {
            log_Error("Attempted to disconnect a non-mysqli object.", "db_error.log");
        }
    }
}

?>
