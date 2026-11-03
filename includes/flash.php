<?php

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!empty($_SESSION['flash'])) {
        $flashType = $_SESSION['flash']['type'] ?? 'info';
        $flashMessage = $_SESSION['flash']['message'] ?? '';

        unset($_SESSION['flash']);

        if ($flashMessage !== '') {
            ?>
    <div class="alert alert-<?php echo htmlspecialchars($flashType, ENT_QUOTES, 'UTF-8'); ?>" role="alert">
    <?php echo htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php
        }
    }