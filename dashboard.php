<?php

    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/auth_check.php';

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

    $summary = Transaction::monthlySummary($currentUserId, $month, $year);

    $pageTitle = 'Dashboard';
    $pageScripts = ['dashboard.js'];

    require_once __DIR__ . '/includes/header.php';

    ?>

    <div class="dashboard-page">
    <div class="page-heading">
    <h1>Welcome, <?php echo htmlspecialchars($currentUserName, ENT_QUOTES, 'UTF-8'); ?></h1>
   <p class="page-subtitle"><?php echo htmlspecialchars($monthNames[$month], ENT_QUOTES, 'UTF-8'); ?> <?php echo $year; ?> summary</p>
   </div>

    <form action="dashboard.php" method="get" class="filter-form" id="dashboardFilterForm">
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

    <div class="summary-grid" data-currency="<?php echo htmlspecialchars(APP_CURRENCY_SYMBOL, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="summary-card summary-card-balance">
    <span class="summary-label">Balance</span>
    <span class="summary-value" data-value="<?php echo $summary['balance']; ?>"><?php echo Format::money($summary['balance']); ?></span>
    </div>
    <div class="summary-card summary-card-income">
    <span class="summary-label">Total Income</span>
    <span class="summary-value" data-value="<?php echo $summary['income']; ?>"><?php echo Format::money($summary['income']); ?></span>
    </div>
    <div class="summary-card summary-card-expense">
    <span class="summary-label">Total Expense</span>
    <span class="summary-value" data-value="<?php echo $summary['expense']; ?>"><?php echo Format::money($summary['expense']); ?></span>
    </div>
    </div>

    <div class="dashboard-actions">
    <a href="transactions.php" class="dashboard-action-card">
    <span class="dashboard-action-title">My Data</span>
    <span class="dashboard-action-desc">View, add, edit, and delete your transactions</span>
    </a>
    <a href="charts.php" class="dashboard-action-card">
    <span class="dashboard-action-title">My Charts</span>
    <span class="dashboard-action-desc">Visualize your income and expenses</span>
    </a>
    </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>