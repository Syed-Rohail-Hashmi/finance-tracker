<?php

    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/auth_check.php';

    use App\Core\Csrf;
    use App\Models\Transaction;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: transactions.php');
        exit;
    }

    if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Your session has expired. Please try again.'];
        header('Location: transactions.php');
        exit;
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id > 0 && Transaction::delete($id, $currentUserId)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaction deleted successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Transaction not found.'];
    }

    header('Location: transactions.php');
    exit;