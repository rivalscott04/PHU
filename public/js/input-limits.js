(function () {
    'use strict';

    const config = window.PHU_INPUT_LIMITS || {};
    const nikFields = new Set(config.nik?.fields || ['nik', 'no_ktp']);
    const phoneFields = new Set(config.phone?.fields || ['nomor_hp', 'no_hp', 'pic_nomor_hp', 'telepon', 'Telepon']);
    const spphFields = new Set(config.spph?.fields || ['nomor_porsi']);
    const nikMax = config.nik?.max || 16;
    const phoneMax = config.phone?.max || 16;
    const spphMax = config.spph?.max || 9;

    function resolveFieldKey(field) {
        return field.getAttribute('name') || field.id || '';
    }

    function resolveMaxLength(field) {
        const kind = resolveKind(field);
        if (kind === 'nik') {
            return nikMax;
        }
        if (kind === 'phone') {
            return phoneMax;
        }
        if (kind === 'spph') {
            return spphMax;
        }

        return parseInt(field.dataset.digitsOnly || field.getAttribute('maxlength') || '32', 10);
    }

    function resolveKind(field) {
        const key = resolveFieldKey(field);
        if (nikFields.has(key)) {
            return 'nik';
        }
        if (phoneFields.has(key)) {
            return 'phone';
        }
        if (spphFields.has(key)) {
            return 'spph';
        }

        return null;
    }

    function isManagedField(field) {
        if (!field || field.tagName !== 'INPUT') {
            return false;
        }

        const type = (field.getAttribute('type') || 'text').toLowerCase();
        if (!['text', 'tel', 'number', 'search'].includes(type)) {
            return false;
        }

        return resolveKind(field) !== null || field.hasAttribute('data-digits-only');
    }

    function prepareField(field) {
        if (!isManagedField(field) || field.readOnly || field.disabled) {
            return;
        }

        const max = resolveMaxLength(field);
        field.setAttribute('maxlength', String(max));
        field.setAttribute('inputmode', 'numeric');
        field.dataset.digitsOnly = String(max);
        sanitizeField(field, max);
    }

    function sanitizeField(field, max) {
        max = max || resolveMaxLength(field);
        const cleaned = field.value.replace(/\D/g, '').slice(0, max);
        const changed = field.value !== cleaned;
        if (changed) {
            field.value = cleaned;
        }

        return changed;
    }

    function showDigitsOnlyHint(field) {
        const container = field.closest('.mb-3, .mb-4, .form-group') || field.parentElement;
        if (!container) {
            return;
        }

        const label = container.querySelector('label')?.textContent?.replace('*', '').trim()
            || field.getAttribute('name')
            || field.id
            || 'Kolom ini';
        const message = field.dataset.digitsHint || (label + ' hanya boleh berisi angka');

        let hint = container.querySelector('.digits-only-hint');
        if (!hint) {
            hint = document.createElement('div');
            hint.className = 'text-danger small mt-1 digits-only-hint d-none';
            hint.setAttribute('role', 'status');
            hint.setAttribute('aria-live', 'polite');
            container.appendChild(hint);
        }

        hint.textContent = message;
        hint.classList.remove('d-none');

        if (field._digitsHintTimer) {
            clearTimeout(field._digitsHintTimer);
        }

        field._digitsHintTimer = setTimeout(function () {
            hint.classList.add('d-none');
        }, 2800);
    }

    function scan(root) {
        root = root || document;
        root.querySelectorAll('input').forEach(prepareField);
    }

    document.addEventListener('beforeinput', function (event) {
        const field = event.target;
        if (!isManagedField(field)) {
            return;
        }

        if (event.isComposing) {
            return;
        }

        const allowedTypes = ['deleteContentBackward', 'deleteContentForward', 'deleteByCut', 'historyUndo', 'historyRedo'];
        if (allowedTypes.includes(event.inputType)) {
            return;
        }

        if (event.inputType === 'insertFromPaste') {
            return;
        }

        if (event.data && /\D/.test(event.data)) {
            event.preventDefault();
            showDigitsOnlyHint(field);
        }
    });

    document.addEventListener('input', function (event) {
        const field = event.target;
        if (!isManagedField(field)) {
            return;
        }

        if (sanitizeField(field)) {
            showDigitsOnlyHint(field);
        }
    });

    document.addEventListener('paste', function (event) {
        const field = event.target;
        if (!isManagedField(field)) {
            return;
        }

        event.preventDefault();
        const max = resolveMaxLength(field);
        const pasted = (event.clipboardData || window.clipboardData).getData('text') || '';
        const cleaned = pasted.replace(/\D/g, '').slice(0, max);
        if (pasted && pasted !== cleaned) {
            showDigitsOnlyHint(field);
        }
        field.value = cleaned;
        field.dispatchEvent(new Event('input', { bubbles: true }));
    });

    document.addEventListener('drop', function (event) {
        const field = event.target;
        if (!isManagedField(field)) {
            return;
        }

        event.preventDefault();
        showDigitsOnlyHint(field);
    });

    function init() {
        scan();

        if (!document.body) {
            return;
        }

        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) {
                        return;
                    }

                    if (node.matches && node.matches('input')) {
                        prepareField(node);
                    }

                    scan(node);
                });
            });
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
