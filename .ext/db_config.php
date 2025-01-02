<?php

include_once __DIR__ . '/errorlog.php'; // Logger funkció

/**
 * Load .env file and set environment variables, logging errors if necessary.
 *
 * @param string $path Path to the .env file.
 * @return void
 * @throws Exception If the .env file does not exist or cannot be loaded.
 */
if (!function_exists('load_env')) {
    function load_env($path)
    {
        if (!file_exists($path)) {
            log_Error("The .env file does not exist at: $path", "env_error.log");
            throw new Exception("The .env file does not exist at: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Split the line into name and value
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $name = trim($parts[0]);
                $value = trim($parts[1]);

                // Add to $_ENV and $_SERVER if not already set
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }

                if (!array_key_exists($name, $_SERVER)) {
                    $_SERVER[$name] = $value;
                }
            } else {
                log_Error("Invalid line in .env file: $line", "env_error.log");
            }
        }
    }
}

?>
