<script>
    (function () {
        const totalSteps = 3;
        let currentStep = window.pengawasanWizardActiveStep || 1;
        const labels = window.pengawasanWizardStepLabels || [];
        const panels = document.querySelectorAll('[data-pengawasan-step]');
        const progressItems = document.querySelectorAll('#pengawasan-wizard-progress .pengawasan-wizard-step');
        const currentLabel = document.getElementById('pengawasan-wizard-current-label');

        function showStep(step) {
            if (step < 1 || step > totalSteps) {
                return;
            }

            currentStep = step;

            panels.forEach(function (panel) {
                const panelStep = parseInt(panel.getAttribute('data-pengawasan-step'), 10);
                panel.classList.toggle('is-active', panelStep === step);
            });

            progressItems.forEach(function (item) {
                const itemStep = parseInt(item.getAttribute('data-step'), 10);
                item.classList.toggle('is-active', itemStep === step);
                item.classList.toggle('is-done', itemStep < step);
                item.classList.toggle('is-clickable', itemStep < step);
            });

            if (currentLabel && labels[step - 1]) {
                currentLabel.textContent = 'Langkah ' + step + ' dari ' + totalSteps + ': ' + labels[step - 1];
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.querySelectorAll('[data-pengawasan-goto]').forEach(function (button) {
            button.addEventListener('click', function () {
                const target = parseInt(button.getAttribute('data-pengawasan-goto'), 10);
                showStep(target);
            });
        });

        progressItems.forEach(function (item) {
            item.addEventListener('click', function () {
                const target = parseInt(item.getAttribute('data-step'), 10);
                if (target < currentStep) {
                    showStep(target);
                }
            });

            item.addEventListener('keydown', function (event) {
                if (event.key !== 'Enter' && event.key !== ' ') {
                    return;
                }

                const target = parseInt(item.getAttribute('data-step'), 10);
                if (target < currentStep) {
                    event.preventDefault();
                    showStep(target);
                }
            });
        });

        showStep(currentStep);
    })();
</script>
