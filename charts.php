<?php

    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/auth_check.php';

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

    $pageTitle = 'My Charts';
    $pageScripts = ['charts.js'];

    require_once __DIR__ . '/includes/header.php';

    ?>

    <div class="charts-page">
    <div class="page-heading">
    <h1>My Charts</h1>
    <p class="page-subtitle">Visualize your income and expenses</p>
    </div>

    <form action="charts.php" method="get" class="filter-form" id="chartsFilterForm">
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

    <div class="chart-card">
    <h2 id="chartPieTitle">Expense Breakdown &mdash; <?php echo htmlspecialchars($monthNames[$month], ENT_QUOTES, 'UTF-8'); ?> <?php echo $year; ?></h2>
    <div class="chart-image-wrapper" data-chart="pie">
    <div class="chart-skeleton"></div>
    <img src="chart_pie.php?month=<?php echo $month; ?>&amp;year=<?php echo $year; ?>" alt="Expense breakdown pie chart for <?php echo htmlspecialchars($monthNames[$month], ENT_QUOTES, 'UTF-8'); ?> <?php echo $year; ?>" class="chart-image is-loaded">
    </div>
    </div>

    <div class="chart-card">
    <h2 id="chartLineTitle">Monthly Income vs. Expense Trend &mdash; <?php echo $year; ?></h2>
    <div class="chart-image-wrapper" data-chart="line">
    <div class="chart-skeleton"></div>
    <img src="chart_line.php?year=<?php echo $year; ?>" alt="Monthly income versus expense line chart for <?php echo $year; ?>" class="chart-image is-loaded">
    </div>
    </div>

    <div class="chart-card">
    <h2 id="chartBarTitle">Monthly Income vs. Expense Comparison &mdash; <?php echo $year; ?></h2>
    <div class="chart-image-wrapper" data-chart="bar">
    <div class="chart-skeleton"></div>
    <img src="chart_bar.php?year=<?php echo $year; ?>" alt="Monthly income versus expense bar chart for <?php echo $year; ?>" class="chart-image is-loaded">
    </div>
    </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>