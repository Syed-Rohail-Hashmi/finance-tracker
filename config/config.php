<?php

    require_once __DIR__ . '/database.php';

    date_default_timezone_set('UTC');

    define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
    define('APP_NAME', 'Finance Tracker');
    define('APP_CURRENCY_SYMBOL', '$');

    if (APP_ENV === 'development') {
        ini_set('display_errors', '1');
    } else {
        ini_set('display_errors', '0');
    }

    error_reporting(E_ALL);
    ini_set('log_errors', '1');