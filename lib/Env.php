<?php

class Env
{
    /**
     * Muat file .env ke dalam $_ENV, $_SERVER, dan Core Framework
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line) || $line[0] === '#' || strpos($line, ';') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            $lower = strtolower($value);
            if ($lower === 'true') {
                $parsedVal = true;
            } elseif ($lower === 'false') {
                $parsedVal = false;
            } elseif ($lower === 'null') {
                $parsedVal = null;
            } else {
                $parsedVal = $value;
            }

            $_ENV[$key] = $parsedVal;
            $_SERVER[$key] = $parsedVal;
            putenv("$key=$value");

            if (class_exists('Core', false)) {
                Core::instance()->set($key, $parsedVal);
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        $val = getenv($key);
        return ($val !== false) ? $val : $default;
    }
}
