<?php

namespace App\Services;

use App\Core\Database;

class ChartDataService
{
    public static function expenseBreakdown(int $userId, int $month, int $year): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT c.name AS category, SUM(t.amount) AS total
                                FROM transactions t
                                JOIN categories c ON c.id = t.category_id
                                WHERE t.user_id = ? AND c.type = ? AND MONTH(t.transaction_date) = ? AND YEAR(t.transaction_date) = ?
                                GROUP BY c.id, c.name
                                HAVING SUM(t.amount) > 0
                                ORDER BY total DESC');
        $stmt->execute([$userId, 'expense', $month, $year]);

        return $stmt->fetchAll();
    }
    
    public static function monthlyIncomeExpense(int $userId, int $year): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare('SELECT MONTH(t.transaction_date) AS month, c.type AS type, SUM(t.amount) AS total
                                FROM transactions t
                                JOIN categories c ON c.id = t.category_id
                                WHERE t.user_id = ? AND YEAR(t.transaction_date) = ?
                                GROUP BY MONTH(t.transaction_date), c.type');
        $stmt->execute([$userId, $year]);

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $income = array_fill(0, 12, 0.0);
        $expense = array_fill(0, 12, 0.0);

        foreach ($stmt->fetchAll() as $row) {
            $index = ((int) $row['month']) - 1;

            if ($row['type'] === 'income') {
                $income[$index] = (float) $row['total'];
            } else {
                $expense[$index] = (float) $row['total'];
            }
        }

        return [
            'labels' => $labels,
            'income' => $income,
            'expense' => $expense,
        ];
    }

    private function __construct()
    {
    }
}