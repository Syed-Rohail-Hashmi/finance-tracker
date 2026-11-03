<?php

    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/auth_check.php';

    use App\Core\Csrf;
    use App\Core\Format;
    use App\Models\Transaction;

    $currentMonth = (int) date('n');
    $currentYear = (int) date('Y');

    $month = isset($_GET['month']) && (int) $_GET['month'] >= 1 && (int) $_GET['month'] <= 12
        ? (int) $_GET['month']
        : $currentMonth;

    $year = isset($_GET['year']) && (int) $_GET['year'] >= 2000 && (int) $_GET['year'] <= 2100
        ? (int) $_GET['year']
        : $currentYear;

    $transactions = Transaction::findAllForUser($currentUserId, $month, $year);

    $availableYears = Transaction::distinctYears($currentUserId);

    if (!in_array($currentYear, $availableYears, true)) {
        $availableYears[] = $currentYear;
    }

    if (!in_array($year, $availableYears, true)) {
        $availableYears[] = $year;
    }

    rsort($availableYears);

    $monthNames = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    $pageTitle = 'My Data';

    require_once __DIR__ . '/includes/header.php';

    ?>

    <div class="transactions-page">
    <div class="page-heading page-heading-with-action">
    <div>
    <h1>My Data</h1>
    <p class="page-subtitle">View and manage your transactions</p>
    </div>
    <a href="transaction_form.php?action=add" class="btn btn-primary">Add New Transaction</a>
    </div>

    <form action="transactions.php" method="get" class="filter-form">
    <div class="form-group">
    <label for="month">Month</label>
    <select id="month" name="month" class="form-control">
    <?php foreach ($monthNames as $num => $name): ?>
    <option value="<?php echo $num; ?>"<?php echo $num === $month ? ' selected' : ''; ?>><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></option>
    <?php endforeach; ?>
    </select>
    </div>

    <div class="form-group">
    <label for="year">Year</label>
    <select id="year" name="year" class="form-control">
    <?php foreach ($availableYears as $yearOption): ?>
    <option value="<?php echo $yearOption; ?>"<?php echo $yearOption === $year ? ' selected' : ''; ?>><?php echo $yearOption; ?></option>
    <?php endforeach; ?>
    </select>
    </div>

    <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    <?php if (empty($transactions)): ?>
    <div class="empty-state">
    <p>No transactions found for <?php echo htmlspecialchars($monthNames[$month], ENT_QUOTES, 'UTF-8'); ?> <?php echo $year; ?>.</p>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
    <table class="data-table">
    <thead>
    <tr>
    <th>Date</th>
    <th>Category</th>
    <th>Type</th>
    <th>Amount</th>
    <th>Description</th>
    <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($transactions as $transaction): ?>
    <?php $type = $transaction['category_type'] ?? 'expense'; ?>
    <tr>
    <td data-label="Date"><?php echo htmlspecialchars(date('M j, Y', strtotime($transaction['transaction_date'])), ENT_QUOTES, 'UTF-8'); ?></td>
    <td data-label="Category"><?php echo htmlspecialchars($transaction['category_name'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></td>
    <td data-label="Type">
    <span class="badge badge-<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(ucfirst($type), ENT_QUOTES, 'UTF-8'); ?></span>
    </td>
    <td data-label="Amount" class="amount-cell amount-<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"><?php echo ($type === 'income' ? '+' : '-') . Format::money((float) $transaction['amount']); ?></td>
    <td data-label="Description"><?php echo $transaction['description'] !== null && $transaction['description'] !== '' ? htmlspecialchars($transaction['description'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">&mdash;</span>'; ?></td>
    <td data-label="Actions" class="actions-cell">
    <a href="transaction_form.php?action=edit&amp;id=<?php echo (int) $transaction['id']; ?>" class="btn btn-small btn-outline">Edit</a>
    <form action="delete_transaction.php" method="post" class="inline-form" onsubmit="return confirm('Delete this transaction? This cannot be undone.');">
    <?php echo Csrf::field(); ?>
    <input type="hidden" name="id" value="<?php echo (int) $transaction['id']; ?>">
    <button type="submit" class="btn btn-small btn-danger">Delete</button>
    </form>
    </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    </div>
    <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>