    </main>

    <footer class="site-footer">
    <div class="site-footer-inner">
    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?>. All rights reserved.</p>
    </div>
    </footer>

    <script src="assets/js/app.js"></script>
    <?php if (!empty($pageScripts) && is_array($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
    <script src="assets/js/<?php echo htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
    <?php endif; ?>
    </body>
    </html>