<?php

    require_once __DIR__ . '/config/config.php';

    use App\Core\Auth;

    if (Auth::isLoggedIn()) {
        header('Location: dashboard.php');
    } else {
        header('Location: login.php');
    }

    exit;