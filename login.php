<?php

    require_once __DIR__ . '/config/config.php';

    use App\Core\Auth;
    use App\Core\Csrf;
    use App\Core\Validator;

    if (Auth::isLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }

    $errors = [];
    $email = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Your session has expired. Please try again.';
        } else {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!Validator::required($email) || !Validator::isEmail($email)) {
                $errors[] = 'A valid email address is required.';
            }

            if (!Validator::required($password)) {
                $errors[] = 'Password is required.';
            }

            if (empty($errors)) {
                if (Auth::login($email, $password)) {
                    header('Location: dashboard.php');
                    exit;
                }

                $errors[] = 'Incorrect email or password.';
            }
        }
    }

    $pageTitle = 'Login';
    $pageScripts = ['password.js'];

    require_once __DIR__ . '/includes/header.php';

    ?>

    <div class="auth-page">
    <div class="auth-card">
    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-subtitle">Log in to continue tracking your finances.</p>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error" role="alert">
    <ul>
    <?php foreach ($errors as $error): ?>
    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
    <?php endforeach; ?>
    </ul>
    </div>
    <?php endif; ?>

    <form action="login.php" method="post" novalidate>
    <?php echo Csrf::field(); ?>

    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-group">
    <label for="password">Password</label>
    <div class="password-field">
    <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
    <button type="button" class="password-toggle" data-target="password" aria-label="Show password" aria-pressed="false">
    <svg class="icon-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
    <svg class="icon-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-5.94M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
    </button>
    </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <p class="auth-footer-text">Don't have an account? <a href="register.php">Register</a></p>
    </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>