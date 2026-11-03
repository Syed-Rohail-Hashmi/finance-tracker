<?php

namespace App\Core;

use App\Models\User;
use PDOException;

class Auth
{
    public static function register(string $fullName, string $email, string $password): ?int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $userId = User::create($fullName, $email, $hash);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }

        self::startSession($userId, $fullName);

        return $userId;
    }

    public static function login(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            return false;
        }

        self::startSession((int) $user['id'], $user['full_name']);

        return true;
    }

    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return !empty($_SESSION['user_id']);
    }

    public static function userId(): ?int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function fullName(): ?string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return $_SESSION['full_name'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    private static function startSession(int $userId, string $fullName): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        $_SESSION['full_name'] = $fullName;
    }

    private function __construct()
    {
    }
}