<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use App\Core\Auth;

    Auth::requireLogin();

    $currentUserId = Auth::userId();
    $currentUserName = Auth::fullName();