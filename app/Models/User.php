<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, full_name, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, full_name, email, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    public static function create(string $fullName, string $email, string $passwordHash): int
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$fullName, $email, $passwordHash]);

        return (int) $pdo->lastInsertId();
    }

    public static function emailExists(string $email): bool
    {
        return self::findByEmail($email) !== null;
    }

    private function __construct()
    {
    }
}