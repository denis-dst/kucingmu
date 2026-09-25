

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Global safeguard to prevent accidental double-clicks and multiple form submissions across all actions
const SPINNER_SVG = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-current shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

function unlockButton(btn) {
    if (!btn) return;
    btn.disabled = false;
    btn.removeAttribute('aria-disabled');
    btn.classList.remove('opacity-70', 'cursor-not-allowed', 'pointer-events-none', 'is-submitting');
    if (btn.dataset.originalContent) {
        btn.innerHTML = btn.dataset.originalContent;
        delete btn.dataset.originalContent;
    }
    delete btn.dataset.clickLocked;
}

function unlockForm(form) {
    if (!form) return;
    form.dataset.submitting = 'false';
    const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
    submitBtns.forEach(unlockButton);
}

// 1. Double-Submit Prevention for All Forms
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form || !(form instanceof HTMLFormElement)) return;

    // Check if form is already in the middle of being submitted
    if (form.dataset.submitting === 'true') {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }

    const method = (form.getAttribute('method') || 'GET').toUpperCase();
    if (method !== 'GET') {
        form.dataset.submitting = 'true';

        const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        submitBtns.forEach((btn) => {
            if (!btn.dataset.originalContent) {
                btn.dataset.originalContent = btn.innerHTML;
            }

            // Immediately disable pointer events to stop rapid spam clicking
            btn.classList.add('opacity-70', 'cursor-not-allowed', 'pointer-events-none', 'is-submitting');
            btn.setAttribute('aria-disabled', 'true');

            // Prepend spinner if button is a button element with visible text
            if (btn.tagName === 'BUTTON' && !btn.innerHTML.includes('animate-spin')) {
                btn.innerHTML = SPINNER_SVG + '<span>' + btn.innerText.trim() + '...</span>';
            }

            // Formally set disabled on next tick so form payload completes
            setTimeout(() => {
                btn.disabled = true;
            }, 30);
        });

        // Safety timeout: Re-enable form and buttons after 9 seconds if page did not reload
        setTimeout(() => {
            unlockForm(form);
        }, 9000);
    }
}, true);

// 2. Rapid Multi-Click Debounce on Action Buttons and Links
document.addEventListener('click', (e) => {
    const target = e.target.closest('button, input[type="submit"], input[type="button"], a.button-primary, a.button-secondary, a.button-danger, a.btn-action-primary, a.btn-action-danger, a.btn-action-success');
    if (!target) return;

    // If button is currently locked against repeated clicks
    if (target.dataset.clickLocked === 'true' || target.getAttribute('aria-disabled') === 'true' || target.classList.contains('pointer-events-none')) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }

    // Debounce rapid repeated clicks for 800ms
    target.dataset.clickLocked = 'true';
    setTimeout(() => {
        delete target.dataset.clickLocked;
    }, 800);
}, true);

// 3. Restore all buttons and forms when restored from browser cache (Back / Forward navigation)
window.addEventListener('pageshow', () => {
    document.querySelectorAll('form').forEach(unlockForm);
    document.querySelectorAll('button, a, input').forEach(unlockButton);
});

