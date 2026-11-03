<?php

namespace App\Models;

use App\Core\Database;

class Transaction
{
    public static function findAllForUser(int $userId, ?int $month = null, ?int $year = null): array
    {
        $pdo = Database::getConnection();

        $sql = 'SELECT t.id, t.amount, t.description, t.transaction_date, t.category_id,
                       c.name AS category_name, c.type AS category_type
                FROM transactions t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.user_id = ?';

        $params = [$userId];

        if ($month !== null && $year !== null) {
            $sql .= ' AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?';
            $params[] = $month;
            $params[] = $year;
        }

        $sql .= ' ORDER BY t.transaction_date DESC, t.id DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function findById(int $id, int $userId): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT id, category_id, amount, description, transaction_date
                                FROM transactions
                                WHERE id = ? AND user_id = ?
                                LIMIT 1');
        $stmt->execute([$id, $userId]);

        $transaction = $stmt->fetch();

        return $transaction !== false ? $transaction : null;
    }

    public static function create(int $userId, int $categoryId, float $amount, ?string $description, string $transactionDate): int
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('INSERT INTO transactions (user_id, category_id, amount, description, transaction_date)
                                VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $categoryId, $amount, $description, $transactionDate]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, int $userId, int $categoryId, float $amount, ?string $description, string $transactionDate): bool
    {
        if (self::findById($id, $userId) === null) {
            return false;
        }

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('UPDATE transactions
                                SET category_id = ?, amount = ?, description = ?, transaction_date = ?
                                WHERE id = ? AND user_id = ?');
        $stmt->execute([$categoryId, $amount, $description, $transactionDate, $id, $userId]);

        return true;
    }

    public static function delete(int $id, int $userId): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('DELETE FROM transactions WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }

    public static function monthlySummary(int $userId, int $month, int $year): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT c.type AS type, COALESCE(SUM(t.amount), 0) AS total
                                FROM transactions t
                                JOIN categories c ON c.id = t.category_id
                                WHERE t.user_id = ? AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?
                                GROUP BY c.type');
        $stmt->execute([$userId, $month, $year]);

        $totals = ['income' => 0.0, 'expense' => 0.0];

        foreach ($stmt->fetchAll() as $row) {
            $totals[$row['type']] = (float) $row['total'];
        }

        $totals['balance'] = $totals['income'] - $totals['expense'];

        return $totals;
    }

    public static function distinctYears(int $userId): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT DISTINCT YEAR(transaction_date) AS year
                                FROM transactions
                                WHERE user_id = ?
                                ORDER BY year DESC');
        $stmt->execute([$userId]);

        return array_map('intval', array_column($stmt->fetchAll(), 'year'));
    }

    private function __construct()
    {
    }
}