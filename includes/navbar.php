<?php

    use App\Core\Auth;

    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    $isLoggedIn = Auth::isLoggedIn();

    ?>
    <header class="navbar">
    <div class="navbar-inner">
    <a href="index.php" class="navbar-brand"><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></a>

    <button type="button" class="navbar-toggle" id="navbarToggle" aria-expanded="false" aria-controls="navbarMenu" aria-label="Toggle navigation menu">
    <span class="navbar-toggle-bar"></span>
    <span class="navbar-toggle-bar"></span>
    <span class="navbar-toggle-bar"></span>
    </button>

    <nav class="navbar-menu" id="navbarMenu" aria-label="Main navigation">
    <?php if ($isLoggedIn): ?>
    <a href="dashboard.php" class="navbar-link<?php echo $currentPage === 'dashboard.php' ? ' is-active' : ''; ?>">Dashboard</a>
    <a href="transactions.php" class="navbar-link<?php echo in_array($currentPage, ['transactions.php', 'transaction_form.php'], true) ? ' is-active' : ''; ?>">My Data</a>
    <a href="charts.php" class="navbar-link<?php echo $currentPage === 'charts.php' ? ' is-active' : ''; ?>">My Charts</a>
    <div class="navbar-user">
    <span class="navbar-user-name"><?php echo htmlspecialchars(Auth::fullName(), ENT_QUOTES, 'UTF-8'); ?></span>
    <a href="logout.php" class="navbar-logout">Logout</a>
    </div>
    <?php else: ?>
    <a href="login.php" class="navbar-link<?php echo $currentPage === 'login.php' ? ' is-active' : ''; ?>">Login</a>
    <a href="register.php" class="navbar-link navbar-link-cta<?php echo $currentPage === 'register.php' ? ' is-active' : ''; ?>">Register</a>
    <?php endif; ?>
    </nav>
    </div>
    </header>