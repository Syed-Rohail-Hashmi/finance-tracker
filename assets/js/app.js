    (function () {
        var toggleButton = document.getElementById('navbarToggle');
        var menu = document.getElementById('navbarMenu');
        var desktopQuery = window.matchMedia('(min-width: 1024px)');

        function closeMenu() {
            menu.classList.remove('is-open');
            toggleButton.classList.remove('is-active');
            toggleButton.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('nav-open');
        }

        function openMenu() {
            menu.classList.add('is-open');
            toggleButton.classList.add('is-active');
            toggleButton.setAttribute('aria-expanded', 'true');
            document.body.classList.add('nav-open');
        }

        if (toggleButton && menu) {
            toggleButton.addEventListener('click', function () {
                if (menu.classList.contains('is-open')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            menu.addEventListener('click', function (event) {
                if (event.target.closest('a')) {
                    closeMenu();
                }
            });

            document.addEventListener('click', function (event) {
                if (!menu.classList.contains('is-open')) {
                    return;
                }

                if (menu.contains(event.target) || toggleButton.contains(event.target)) {
                    return;
                }

                closeMenu();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && menu.classList.contains('is-open')) {
                    closeMenu();
                    toggleButton.focus();
                }
            });

            desktopQuery.addEventListener('change', function (event) {
                if (event.matches) {
                    closeMenu();
                }
            });
        }

        var successAlerts = document.querySelectorAll('.alert-success');

        successAlerts.forEach(function (alert) {
            setTimeout(function () {
                alert.classList.add('is-dismissing');

                setTimeout(function () {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 400);
            }, 4000);
        });
    })();