    (function () {
        var toggleButtons = document.querySelectorAll('.password-toggle');

        toggleButtons.forEach(function (button) {
            var targetId = button.getAttribute('data-target');
            var input = targetId ? document.getElementById(targetId) : null;

            if (!input) {
                return;
            }

            button.addEventListener('click', function () {
                var willShow = input.type === 'password';

                input.type = willShow ? 'text' : 'password';
                button.classList.toggle('is-visible', willShow);
                button.setAttribute('aria-pressed', willShow ? 'true' : 'false');
                button.setAttribute('aria-label', willShow ? 'Hide password' : 'Show password');
            });
        });

        var passwordInput = document.getElementById('password');
        var requirementsList = document.getElementById('passwordRequirements');
        var strengthContainer = document.getElementById('passwordStrength');
        var strengthFill = document.getElementById('passwordStrengthFill');
        var strengthLabel = document.getElementById('passwordStrengthLabel');

        if (!passwordInput || !requirementsList || !strengthContainer || !strengthFill || !strengthLabel) {
            return;
        }

    var ruleTests = {
            uppercase: function (value) {
                return /[A-Z]/.test(value);
            },
            lowercase: function (value) {
                return /[a-z]/.test(value);
            },
            number: function (value) {
                return /[0-9]/.test(value);
            },
            special: function (value) {
                return /[^A-Za-z0-9\s]/.test(value);
            },
            length: function (value) {
                return value.length >= 8;
            }
        };

        var strengthWidths = {
            weak: '25%',
            medium: '50%',
            strong: '75%',
            'very-strong': '100%'
        };

        var strengthLabels = {
            weak: 'Weak',
            medium: 'Medium',
            strong: 'Strong',
            'very-strong': 'Very Strong'
        };
        function updatePasswordFeedback() {
            var value = passwordInput.value;
            var metCount = 0;

            requirementsList.querySelectorAll('[data-rule]').forEach(function (item) {
                var rule = item.getAttribute('data-rule');
                var test = ruleTests[rule];
                var passed = test ? test(value) : false;
                var icon = item.querySelector('.req-icon');

                item.classList.toggle('is-met', passed);

            if (icon) {
                    icon.textContent = passed ? '✔' : '';
                }

                if (passed) {
                    metCount += 1;
                }
            });

        strengthContainer.classList.remove('is-weak', 'is-medium', 'is-strong', 'is-very-strong');

            if (value.length === 0) {
                strengthFill.style.width = '0%';
                strengthLabel.textContent = '';
                return;
            }

            var strength = 'weak';

            if (metCount === 5) {
                strength = 'very-strong';
            } else if (metCount === 4) {
                strength = 'strong';
            } else if (metCount >= 2) {
                strength = 'medium';
            }

            strengthContainer.classList.add('is-' + strength);
            strengthFill.style.width = strengthWidths[strength];
            strengthLabel.textContent = strengthLabels[strength];
        }

        passwordInput.addEventListener('input', updatePasswordFeedback);
        updatePasswordFeedback();
    })();