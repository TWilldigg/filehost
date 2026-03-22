<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function loadEnv(string $filePath): array {
    $env = [];

    if (!file_exists($filePath)) {
        return $env;
    }

    foreach (file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        // Skip comments and invalid lines
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value, "\"' "); // remove quotes and extra spaces

        $env[$key] = $value;
    }

    return $env;
}
