

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Global safeguard to prevent accidental double-submits across standard POST forms
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form || !(form instanceof HTMLFormElement)) return;

    if (form.dataset.submitting === 'true') {
        e.preventDefault();
        return false;
    }

    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method !== 'GET') {
        form.dataset.submitting = 'true';

        const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitBtns.forEach((btn) => {
            if (!btn.disabled) {
                setTimeout(() => {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-not-allowed');
                }, 50);
            }
        });

        // Safety timeout to re-enable in case the submission was cancelled or failed client-side
        setTimeout(() => {
            form.dataset.submitting = 'false';
            submitBtns.forEach((btn) => {
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
            });
        }, 8000);
    }
});

