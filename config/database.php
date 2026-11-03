<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();

    define('DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
    define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
    define('DB_NAME', $_ENV['DB_NAME'] ?? 'finance_tracker');
    define('DB_USER', $_ENV['DB_USER'] ?? 'root');
    define('DB_PASS', $_ENV['DB_PASS'] ?? '');