import Swal from 'sweetalert2';

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    customClass: {
        popup: 'rounded-xl shadow-lg',
    },
});

export function notifySuccess(message) {
    if (!message) return;
    return toast.fire({ icon: 'success', title: message });
}

export function notifyError(message) {
    if (!message) return;
    return toast.fire({ icon: 'error', title: message });
}

export function notifyInfo(message) {
    if (!message) return;
    return toast.fire({ icon: 'info', title: message });
}

export function notifyWarning(message) {
    if (!message) return;
    return toast.fire({ icon: 'warning', title: message });
}

export function showFlashFrom(root = document) {
    const el = root.querySelector?.('[data-admin-flash]') || document.querySelector('[data-admin-flash]');
    if (!el) return;

    const { success, error, warning, info } = el.dataset;
    el.remove();

    if (success) notifySuccess(success);
    if (error) notifyError(error);
    if (warning) notifyWarning(warning);
    if (info) notifyInfo(info);
}

function itemNameFromForm(form) {
    const row = form.closest('tr');
    if (!row) return 'this item';

    const firstCell = row.querySelector('td:first-child');
    if (!firstCell) return 'this item';

    const title =
        firstCell.querySelector('p.font-medium, .font-semibold, h3, h4, div.text-sm') || firstCell;

    return title.textContent.trim().split('\n')[0] || 'this item';
}

export function confirmAction({
    title = 'Are you sure?',
    html,
    text,
    icon = 'warning',
    confirmButtonText = 'Yes, continue',
    confirmButtonColor = '#dc2626',
} = {}) {
    return Swal.fire({
        title,
        html,
        text,
        icon,
        showCancelButton: true,
        confirmButtonColor,
        cancelButtonColor: '#6b7280',
        confirmButtonText,
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            popup: 'rounded-2xl shadow-2xl',
            confirmButton: 'px-6 py-2.5 rounded-xl font-semibold',
            cancelButton: 'px-6 py-2.5 rounded-xl font-semibold',
        },
    });
}

function bindConfirmForms(root = document) {
    const forms = root.querySelectorAll(
        '.delete-form, .toggle-form, form[data-confirm], form[action*="destroy"], form[action*="delete"], form[action*="toggle-status"], form[action*="toggle"]'
    );

    forms.forEach((form) => {
        if (form.dataset.sweetalert === 'true') return;
        form.dataset.sweetalert = 'true';

        form.addEventListener('submit', async (event) => {
            if (form.dataset.sweetalertConfirmed === 'true') {
                delete form.dataset.sweetalertConfirmed;
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const customMessage = form.dataset.confirm;
            const isToggle =
                form.classList.contains('toggle-form') ||
                (form.getAttribute('action') || '').includes('toggle');
            const button = form.querySelector('button[type="submit"]');
            const actionLabel = button?.textContent?.trim() || (isToggle ? 'toggle' : 'delete');
            const isDisable = actionLabel.toLowerCase().includes('disable');
            const itemName = itemNameFromForm(form);

            let result;
            if (customMessage) {
                result = await confirmAction({
                    title: 'Please confirm',
                    text: customMessage,
                    icon: 'warning',
                    confirmButtonText: 'Yes, continue',
                    confirmButtonColor: '#dc2626',
                });
            } else if (isToggle) {
                result = await confirmAction({
                    title: isDisable ? 'Disable this item?' : 'Enable this item?',
                    html: `<div class="text-left"><p class="mb-2">You are about to ${isDisable ? 'disable' : 'enable'}:</p><p class="font-semibold text-slate-900">"${itemName}"</p></div>`,
                    icon: isDisable ? 'warning' : 'question',
                    confirmButtonText: `Yes, ${actionLabel}!`,
                    confirmButtonColor: isDisable ? '#f59e0b' : '#10b981',
                });
            } else {
                result = await confirmAction({
                    title: 'Are you sure?',
                    html: `<div class="text-left"><p class="mb-2">You are about to delete:</p><p class="font-semibold text-slate-900">"${itemName}"</p><p class="mt-2 text-sm text-red-600">This action cannot be undone!</p></div>`,
                    icon: 'warning',
                    confirmButtonText: 'Yes, delete it!',
                    confirmButtonColor: '#dc2626',
                });
            }

            if (result.isConfirmed) {
                form.dataset.sweetalertConfirmed = 'true';
                form.requestSubmit();
            }
        });
    });
}

export function bindConfirmLinks(root = document) {
    root.querySelectorAll('[data-confirm]:not(form)').forEach((el) => {
        if (el.dataset.sweetalert === 'true') return;
        el.dataset.sweetalert = 'true';

        el.addEventListener('click', async (event) => {
            if (el.dataset.sweetalertConfirmed === 'true') {
                delete el.dataset.sweetalertConfirmed;
                return;
            }

            event.preventDefault();
            const result = await confirmAction({
                title: 'Please confirm',
                text: el.dataset.confirm || 'Are you sure?',
            });

            if (result.isConfirmed) {
                el.dataset.sweetalertConfirmed = 'true';
                el.click();
            }
        });
    });
}

export function bindNotifyTriggers(root = document) {
    root.querySelectorAll('[data-admin-notify]').forEach((el) => {
        if (el.dataset.sweetalert === 'true') return;
        el.dataset.sweetalert = 'true';
        el.addEventListener('click', (event) => {
            event.preventDefault();
            notifyInfo(el.dataset.adminNotify);
        });
    });
}

export function initSweetAlert(root = document) {
    showFlashFrom(root);
    bindConfirmForms(root);
    bindConfirmLinks(root);
    bindNotifyTriggers(root);
}

export { Swal };
