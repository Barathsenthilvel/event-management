/**
 * Shows a spinner inside submit buttons on form submit (admin, member, public).
 * Skips AJAX forms that call preventDefault() on submit.
 */
(function () {
    'use strict';

    var SPINNER_HTML =
        '<svg class="gnat-btn-spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
        '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
        '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
        '</svg>';

    function isSubmitControl(el) {
        if (!el) return false;
        if (el.tagName === 'BUTTON') {
            var type = (el.getAttribute('type') || 'submit').toLowerCase();
            return type === 'submit';
        }
        return el.tagName === 'INPUT' && (el.getAttribute('type') || '').toLowerCase() === 'submit';
    }

    function getSubmitButton(form, submitter) {
        if (isSubmitControl(submitter)) {
            return submitter;
        }
        if (submitter && submitter.form === form && submitter.tagName === 'BUTTON') {
            return submitter;
        }
        return form.querySelector('button[type="submit"], input[type="submit"]');
    }

    function defaultLoadingLabel(button) {
        var custom = button.getAttribute('data-loading-text');
        if (custom) return custom;

        var idle = button.querySelector('.gnat-submit-idle, .signin-btn-idle');
        var source = idle ? idle.textContent : button.textContent;
        var text = (source || '').replace(/\s+/g, ' ').trim();

        if (/sign\s*in/i.test(text)) return 'Signing in…';
        if (/sign\s*up|register|create account/i.test(text)) return 'Creating account…';
        if (/verify|confirm/i.test(text)) return 'Verifying…';
        if (/delete|remove/i.test(text)) return 'Deleting…';
        if (/approve/i.test(text)) return 'Approving…';
        if (/reject/i.test(text)) return 'Rejecting…';
        if (/save|update/i.test(text)) return 'Saving…';
        if (/send/i.test(text)) return 'Sending…';
        if (/submit|apply/i.test(text)) return 'Submitting…';
        if (/continue|proceed|pay/i.test(text)) return 'Please wait…';
        if (/logout|log\s*out/i.test(text)) return 'Logging out…';

        return text ? text + '…' : 'Please wait…';
    }

    function activate(button, options) {
        if (!button || button.dataset.submitLoading === '1') return;
        if (button.dataset.noSubmitSpinner !== undefined) return;

        options = options || {};
        button.dataset.submitLoading = '1';
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');

        var idle = button.querySelector('.gnat-submit-idle, .signin-btn-idle');
        var loading = button.querySelector('.gnat-submit-loading, .signin-btn-loading');

        if (idle && loading) {
            idle.classList.add('hidden');
            loading.classList.remove('hidden');
            loading.classList.add('inline-flex');
            return;
        }

        if (!button.dataset.submitIdleHtml) {
            button.dataset.submitIdleHtml = button.innerHTML;
        }

        var label = options.label || defaultLoadingLabel(button);
        button.innerHTML =
            '<span class="inline-flex items-center justify-center gap-2 gnat-submit-loading-wrap">' +
            SPINNER_HTML +
            '<span>' + label + '</span>' +
            '</span>';
    }

    function reset(button) {
        if (!button || button.dataset.submitLoading !== '1') return;

        button.disabled = false;
        button.removeAttribute('aria-busy');
        delete button.dataset.submitLoading;

        var idle = button.querySelector('.gnat-submit-idle, .signin-btn-idle');
        var loading = button.querySelector('.gnat-submit-loading, .signin-btn-loading');

        if (idle && loading) {
            idle.classList.remove('hidden');
            loading.classList.add('hidden');
            loading.classList.remove('inline-flex');
            return;
        }

        if (button.dataset.submitIdleHtml) {
            button.innerHTML = button.dataset.submitIdleHtml;
            delete button.dataset.submitIdleHtml;
        }
    }

    function activateForm(form, submitter) {
        if (!form) return;
        var btn = getSubmitButton(form, submitter || null);
        activate(btn);
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (el) {
            if (el !== btn) el.disabled = true;
        });
    }

    function resetForm(form) {
        if (!form) return;
        form.querySelectorAll('[data-submit-loading="1"]').forEach(reset);
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (el) {
            el.disabled = false;
        });
    }

    document.addEventListener(
        'submit',
        function (event) {
            var form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (form.dataset.noSubmitSpinner !== undefined) return;

            var submitter = event.submitter || null;
            if (submitter && submitter.dataset.noSubmitSpinner !== undefined) return;

            setTimeout(function () {
                if (event.defaultPrevented) return;
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;
                activateForm(form, submitter);
            }, 0);
        },
        false
    );

    window.addEventListener('pageshow', function (event) {
        if (!event.persisted) return;
        document.querySelectorAll('[data-submit-loading="1"]').forEach(reset);
    });

    window.gnatSubmitLoading = {
        activate: activate,
        reset: reset,
        activateForm: activateForm,
        resetForm: resetForm,
    };

    window.gnatSetSubmitButtonLoading = activate;
    window.gnatResetSubmitButton = resetForm;
})();
