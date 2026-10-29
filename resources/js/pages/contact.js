// Contact form: submits via fetch for an in-place response, falling back to a
// normal POST (server-side validation and flash messages) when JS is off.
export default function contact() {
    const form = document.querySelector('[data-contact-form]');
    if (!form) return;

    const button = form.querySelector('[data-submit]');
    const buttonLabel = button.querySelector('.btn__label').textContent;
    const success = document.querySelector('[data-form-success]');
    const error = document.querySelector('[data-form-error]');

    const setFieldError = (name, message) => {
        const field = form.querySelector(`[data-field="${name}"]`);
        if (!field) return;
        const input = field.querySelector('input, textarea');
        field.classList.toggle('is-invalid', Boolean(message));
        field.querySelector('[data-field-error]').textContent = message || '';
        if (message) input.setAttribute('aria-invalid', 'true');
        else input.removeAttribute('aria-invalid');
    };

    const showAlert = (el, message) => {
        el.querySelector('span').textContent = message;
        el.hidden = false;
    };

    // Clear a field's error as soon as the visitor edits it.
    form.addEventListener('input', (e) => {
        const field = e.target.closest('[data-field]');
        if (field?.classList.contains('is-invalid')) setFieldError(field.dataset.field, '');
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        success.hidden = true;
        error.hidden = true;
        ['name', 'email', 'subject', 'message'].forEach((name) => setFieldError(name, ''));

        button.disabled = true;
        button.classList.add('is-loading');
        button.querySelector('.btn__label').textContent = 'Sending…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                form.reset();
                document.dispatchEvent(new CustomEvent('contact:sent'));
                showAlert(success, data.message);
                success.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else if (response.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([name, messages]) => setFieldError(name, messages[0]));
                showAlert(error, 'Please fix the highlighted fields and try again.');
                form.querySelector('[aria-invalid="true"]')?.focus();
            } else if (response.status === 419) {
                showAlert(error, 'Your session expired. Please refresh the page and send your message again.');
            } else {
                showAlert(error, data.message || 'Something went wrong. Please try again in a moment.');
            }
        } catch {
            showAlert(error, 'You appear to be offline. Check your connection and try again.');
        } finally {
            button.disabled = false;
            button.classList.remove('is-loading');
            button.querySelector('.btn__label').textContent = buttonLabel;
        }
    });
}
