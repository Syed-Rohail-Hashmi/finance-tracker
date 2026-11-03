    document.addEventListener('DOMContentLoaded', function () {
        var values = document.querySelectorAll('.summary-value[data-value]');

        if (!values.length) {
            return;
        }

        var currencyContainer = document.querySelector('.summary-grid');
        var currency = currencyContainer ? currencyContainer.getAttribute('data-currency') || '' : '';
        var duration = 900;

        function formatAmount(amount) {
            var sign = amount < 0 ? '-' : '';

            return sign + currency + Math.abs(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function animateValue(el) {
            var target = parseFloat(el.getAttribute('data-value'));

            if (isNaN(target)) {
                return;
            }

            if (prefersReducedMotion) {
                el.textContent = formatAmount(target);
                return;
            }

            var startTime = null;

            function step(timestamp) {
                if (!startTime) {
                    startTime = timestamp;
                }

                var progress = Math.min((timestamp - startTime) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);

                el.textContent = formatAmount(target * eased);

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.textContent = formatAmount(target);
                }
            }

            window.requestAnimationFrame(step);
        }

        values.forEach(animateValue);
    });