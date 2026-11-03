    (function () {
        var form = document.querySelector('.form-page form');

        if (!form) {
            return;
        }

        var categoryField = document.getElementById('category_id');
        var amountField = document.getElementById('amount');
        var dateField = document.getElementById('transaction_date');

        function showFieldError(field, message) {
            var errorId = field.id + '_error';
            var errorEl = document.getElementById(errorId);

            if (!errorEl) {
                errorEl = document.createElement('span');
                errorEl.id = errorId;
                errorEl.className = 'field-error';
                field.insertAdjacentElement('afterend', errorEl);
            }

            errorEl.textContent = message;
            field.classList.add('is-invalid');
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', errorId);
        }

        function clearFieldError(field) {
            var errorEl = document.getElementById(field.id + '_error');

            if (errorEl && errorEl.parentNode) {
                errorEl.parentNode.removeChild(errorEl);
            }

            field.classList.remove('is-invalid');
            field.removeAttribute('aria-invalid');
            field.removeAttribute('aria-describedby');
        }

        function validateCategory() {
            if (!categoryField.value) {
                showFieldError(categoryField, 'Please select a valid category.');
                return false;
            }

            clearFieldError(categoryField);
            return true;
        }

        function validateAmount() {
            var value = parseFloat(amountField.value);

            if (isNaN(value) || value <= 0) {
                showFieldError(amountField, 'Amount must be a number greater than 0.');
                return false;
            }

            clearFieldError(amountField);
            return true;
        }

        function validateDate() {
            if (!dateField.value.trim()) {
                showFieldError(dateField, 'Please enter a valid date.');
                return false;
            }

            clearFieldError(dateField);
            return true;
        }

        if (categoryField) {
            categoryField.addEventListener('change', validateCategory);
        }

        if (amountField) {
            amountField.addEventListener('input', validateAmount);
        }

        if (dateField) {
            dateField.addEventListener('change', validateDate);
        }

        form.addEventListener('submit', function (event) {
            var isCategoryValid = categoryField ? validateCategory() : true;
            var isAmountValid = amountField ? validateAmount() : true;
            var isDateValid = dateField ? validateDate() : true;

            if (!isCategoryValid || !isAmountValid || !isDateValid) {
                event.preventDefault();

                var firstInvalid = form.querySelector('.is-invalid');

                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    })();