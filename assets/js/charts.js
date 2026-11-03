    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('chartsFilterForm');

        if (!form) {
            return;
        }

        var monthField = document.getElementById('month');
        var yearField = document.getElementById('year');
        var wrappers = document.querySelectorAll('.chart-image-wrapper[data-chart]');

        function loadChart(wrapper, month, year) {
            var type = wrapper.getAttribute('data-chart');
            var img = wrapper.querySelector('.chart-image');
            var skeleton = wrapper.querySelector('.chart-skeleton');

            if (!img) {
                return;
            }

            var src = type === 'pie'
                ? 'chart_pie.php?month=' + encodeURIComponent(month) + '&year=' + encodeURIComponent(year)
                : 'chart_' + type + '.php?year=' + encodeURIComponent(year);

            if (skeleton) {
                skeleton.classList.add('is-visible');
            }

            img.classList.remove('is-loaded');

            var preload = new Image();

            preload.onload = function () {
                img.src = src;

                if (skeleton) {
                    skeleton.classList.remove('is-visible');
                }

                window.requestAnimationFrame(function () {
                    img.classList.add('is-loaded');
                });
            };

            preload.onerror = function () {
                if (skeleton) {
                    skeleton.classList.remove('is-visible');
                }

                img.classList.add('is-loaded');
            };

            preload.src = src + (src.indexOf('?') > -1 ? '&' : '?') + '_=' + Date.now();
        }

    function updateTitles(month, year) {
            var monthLabel = monthField ? monthField.options[monthField.selectedIndex].text : '';
            var pieTitle = document.getElementById('chartPieTitle');
            var lineTitle = document.getElementById('chartLineTitle');
            var barTitle = document.getElementById('chartBarTitle');

            if (pieTitle) {
                pieTitle.textContent = 'Expense Breakdown \u2014 ' + monthLabel + ' ' + year;
            }

            if (lineTitle) {
                lineTitle.textContent = 'Monthly Income vs. Expense Trend \u2014 ' + year;
            }

            if (barTitle) {
                barTitle.textContent = 'Monthly Income vs. Expense Comparison \u2014 ' + year;
            }
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var month = monthField ? monthField.value : '';
            var year = yearField ? yearField.value : '';

            updateTitles(month, year);

            wrappers.forEach(function (wrapper) {
                loadChart(wrapper, month, year);
            });
        });
    });