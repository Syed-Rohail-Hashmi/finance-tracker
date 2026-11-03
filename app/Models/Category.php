<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->query('SELECT id, name, type FROM categories ORDER BY type ASC, name ASC');

        return $stmt->fetchAll();
    }

    public static function allGroupedByType(): array
    {
        $grouped = ['income' => [], 'expense' => []];

        foreach (self::all() as $category) {
            $grouped[$category['type']][] = $category;
        }

        return $grouped;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, name, type FROM categories WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        $category = $stmt->fetch();

        return $category !== false ? $category : null;
    }

    private function __construct()
    {
    }
}