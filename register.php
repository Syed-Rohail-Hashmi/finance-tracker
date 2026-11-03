<?php

    require_once __DIR__ . '/config/config.php';

    use App\Core\Auth;
    use App\Core\Csrf;
    use App\Core\Validator;
    use App\Models\User;

    if (Auth::isLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }

    $errors = [];
    $fullName = '';
    $email = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Your session has expired. Please try again.';
        } else {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (!Validator::required($fullName)) {
                $errors[] = 'Full name is required.';
            }

            if (!Validator::required($email) || !Validator::isEmail($email)) {
                $errors[] = 'A valid email address is required.';
            } elseif (User::emailExists($email)) {
                $errors[] = 'An account with this email already exists.';
            }

            if (!Validator::isStrongPassword($password)) {
                $errors[] = 'Password must be at least 8 characters and include an uppercase letter, a number, and a special character.';
            }

            if (!Validator::matches($password, $confirmPassword)) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $userId = Auth::register($fullName, $email, $password);

                if ($userId === null) {
                    $errors[] = 'Something went wrong. Please try again.';
                } else {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Welcome, ' . $fullName . '! Your account has been created.'];
                    header('Location: dashboard.php');
                    exit;
                }
            }
        }
    }

    $pageTitle = 'Register';
    $pageScripts = ['password.js'];

    require_once __DIR__ . '/includes/header.php';

    ?>

    <div class="auth-page">
    <div class="auth-card">
    <h1 class="auth-title">Create your account</h1>
    <p class="auth-subtitle">Start tracking your income and expenses in minutes.</p>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error" role="alert">
    <ul>
    <?php foreach ($errors as $error): ?>
    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
    <?php endforeach; ?>
    </ul>
    </div>
    <?php endif; ?>

    <form action="register.php" method="post" novalidate>
    <?php echo Csrf::field(); ?>

    <div class="form-group">
    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-group">
    <label for="password">Password</label>
    <div class="password-field">
    <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" required>
    <button type="button" class="password-toggle" data-target="password" aria-label="Show password" aria-pressed="false">
    <svg class="icon-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
    <svg class="icon-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-5.94M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
    </button>
    </div>

    <div class="password-strength" id="passwordStrength">
    <div class="password-strength-track">
    <div class="password-strength-fill" id="passwordStrengthFill"></div>
    </div>
    <span class="password-strength-label" id="passwordStrengthLabel">Weak</span>
    </div>

    <ul class="password-requirements" id="passwordRequirements">
    <li data-rule="uppercase"><span class="req-icon" aria-hidden="true"></span> At least 1 uppercase letter (A–Z)</li>
    <li data-rule="lowercase"><span class="req-icon" aria-hidden="true"></span> At least 1 lowercase letter (a–z)</li>
    <li data-rule="number"><span class="req-icon" aria-hidden="true"></span> At least 1 number (0–9)</li>
    <li data-rule="special"><span class="req-icon" aria-hidden="true"></span> At least 1 special character (@, #, $, %, etc.)</li>
    <li data-rule="length"><span class="req-icon" aria-hidden="true"></span> Minimum 8 characters</li>
    </ul>
    </div>

    <div class="form-group">
    <label for="confirm_password">Confirm Password</label>
    <div class="password-field">
    <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="new-password" required>
    <button type="button" class="password-toggle" data-target="confirm_password" aria-label="Show password" aria-pressed="false">
    <svg class="icon-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
    <svg class="icon-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-5.94M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
    </button>
    </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>

    <p class="auth-footer-text">Already have an account? <a href="login.php">Login</a></p>
    </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>