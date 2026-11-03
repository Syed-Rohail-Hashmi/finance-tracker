<?php

    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/includes/auth_check.php';

    use App\Core\Csrf;
    use App\Core\Validator;
    use App\Models\Category;
    use App\Models\Transaction;

    $action = $_GET['action'] ?? 'add';
    $transactionId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if (!in_array($action, ['add', 'edit'], true)) {
        header('Location: transactions.php');
        exit;
    }

    $existingTransaction = null;

    if ($action === 'edit') {
        if ($transactionId <= 0) {
            header('Location: transactions.php');
            exit;
        }

        $existingTransaction = Transaction::findById($transactionId, $currentUserId);

        if ($existingTransaction === null) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Transaction not found.'];
            header('Location: transactions.php');
            exit;
        }
    }

    $errors = [];
    $categoryId = $existingTransaction['category_id'] ?? '';
    $amount = $existingTransaction['amount'] ?? '';
    $transactionDate = $existingTransaction['transaction_date'] ?? date('Y-m-d');
    $description = $existingTransaction['description'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!Csrf::verifyToken($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Your session has expired. Please try again.';
        } else {
            $action = $_POST['action'] ?? 'add';
            $transactionId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

            $categoryId = $_POST['category_id'] ?? '';
            $amount = $_POST['amount'] ?? '';
            $transactionDate = trim($_POST['transaction_date'] ?? '');
            $description = trim($_POST['description'] ?? '');

        if ($action === 'edit' && $transactionId <= 0) {
                $errors[] = 'Invalid transaction.';
            }

            if (!Validator::isPositiveInteger($categoryId) || Category::findById((int) $categoryId) === null) {
                $errors[] = 'Please select a valid category.';
            }

          if (!Validator::isPositiveNumber($amount) || (float) $amount > 99999999.99) {
                $errors[] = 'Amount must be a number greater than 0 and no more than 99,999,999.99.';
            }

            if (!Validator::required($transactionDate) || !Validator::isValidDate($transactionDate)) {
                $errors[] = 'Please enter a valid date.';
            }

            if (empty($errors)) {
                $categoryId = (int) $categoryId;
                $amount = (float) $amount;
                $description = $description !== '' ? $description : null;

                if ($action === 'edit' && $transactionId > 0) {
                    $success = Transaction::update($transactionId, $currentUserId, $categoryId, $amount, $description, $transactionDate);
                    $message = 'Transaction updated successfully.';
                } else {
                    Transaction::create($currentUserId, $categoryId, $amount, $description, $transactionDate);
                    $success = true;
                    $message = 'Transaction added successfully.';
                }

                if ($success) {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => $message];
                    header('Location: transactions.php');
                    exit;
                }

                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }

    $groupedCategories = Category::allGroupedByType();

    $pageTitle = $action === 'edit' ? 'Edit Transaction' : 'Add Transaction';
    $pageScripts = ['transaction-form.js'];

    require_once __DIR__ . '/includes/header.php';

    ?>

    <div class="form-page">
    <div class="form-card">
    <h1><?php echo $action === 'edit' ? 'Edit Transaction' : 'Add New Transaction'; ?></h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error" role="alert">
    <ul>
    <?php foreach ($errors as $error): ?>
    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
    <?php endforeach; ?>
    </ul>
    </div>
    <?php endif; ?>

    <form action="transaction_form.php" method="post" novalidate>
    <?php echo Csrf::field(); ?>
    <input type="hidden" name="action" value="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if ($action === 'edit'): ?>
    <input type="hidden" name="id" value="<?php echo $transactionId; ?>">
    <?php endif; ?>

    <div class="form-group">
    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" class="form-control" required>
    <option value="">Select a category</option>
    <optgroup label="Income">
    <?php foreach ($groupedCategories['income'] as $category): ?>
    <option value="<?php echo (int) $category['id']; ?>"<?php echo (int) $categoryId === (int) $category['id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></option>
    <?php endforeach; ?>
    </optgroup>
    <optgroup label="Expense">
    <?php foreach ($groupedCategories['expense'] as $category): ?>
    <option value="<?php echo (int) $category['id']; ?>"<?php echo (int) $categoryId === (int) $category['id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></option>
    <?php endforeach; ?>
    </optgroup>
    </select>
    </div>

    <div class="form-group">
    <label for="amount">Amount</label>
    <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0.01" value="<?php echo htmlspecialchars((string) $amount, ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-group">
    <label for="transaction_date">Date</label>
    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="<?php echo htmlspecialchars($transactionDate, ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>

    <div class="form-group">
    <label for="description">Description <span class="optional-label">(optional)</span></label>
    <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>

    <div class="form-actions">
    <button type="submit" class="btn btn-primary"><?php echo $action === 'edit' ? 'Update Transaction' : 'Add Transaction'; ?></button>
    <a href="transactions.php" class="btn btn-outline">Cancel</a>
    </div>
    </form>
    </div>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>