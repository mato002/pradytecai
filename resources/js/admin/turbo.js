import { start, visit, setConfirmMethod } from '@hotwired/turbo';
import { initSweetAlert, notifySuccess, notifyError, showFlashFrom, Swal, confirmAction } from './sweetalert';

// Expose for inline admin scripts (bulk actions, etc.)
window.Swal = Swal;
window.Turbo = { visit, start };

const MAIN_FRAME = 'admin_main';
const MODAL_FRAME = 'admin_modal';

function modalOverlay() {
    return document.getElementById('admin-modal-overlay');
}

function modalFrame() {
    return document.getElementById(MODAL_FRAME);
}

export function openAdminModal() {
    const overlay = modalOverlay();
    if (!overlay) return;
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.classList.add('admin-modal-open');
}

export function closeAdminModal({ clear = true } = {}) {
    const overlay = modalOverlay();
    const frame = modalFrame();
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        overlay.setAttribute('aria-hidden', 'true');
    }
    document.body.classList.remove('admin-modal-open');
    if (clear && frame) {
        frame.innerHTML = '';
    }
}

function isModalContent(frame) {
    if (!frame) return false;
    if (frame.querySelector('[data-admin-modal-complete]')) return 'complete';
    if (frame.querySelector('.admin-modal-panel, form')) return 'form';
    return false;
}

function wireModalCloseButtons(root = document) {
    root.querySelectorAll('[data-admin-modal-close]').forEach((btn) => {
        if (btn.dataset.bound === 'true') return;
        btn.dataset.bound = 'true';
        btn.addEventListener('click', (event) => {
            if (!document.body.classList.contains('admin-modal-open')) {
                return;
            }
            event.preventDefault();
            closeAdminModal();
        });
    });
}

function markAdminLinks(root = document) {
    root.querySelectorAll('a[href]').forEach((link) => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }

        let url;
        try {
            url = new URL(href, window.location.origin);
        } catch {
            return;
        }

        if (url.origin !== window.location.origin) return;
        if (!url.pathname.startsWith('/admin')) return;
        if (link.hasAttribute('data-turbo-frame')) return;
        if (link.hasAttribute('data-turbo-full') || link.dataset.turbo === 'false') return;

        const isCrudForm =
            !url.pathname.includes('/settings/') &&
            (/\/create(\/|$)/.test(url.pathname) ||
                /\/\d+\/edit(\/|$)/.test(url.pathname) ||
                /\/[^/]+\/edit(\/|$)/.test(url.pathname));

        if (isCrudForm) {
            link.setAttribute('data-turbo-frame', MODAL_FRAME);
            return;
        }

        if (!link.closest(`#${MODAL_FRAME}`)) {
            link.setAttribute('data-turbo-frame', MAIN_FRAME);
        }
    });

    root.querySelectorAll(`#${MODAL_FRAME} form`).forEach((form) => {
        if (!form.hasAttribute('data-turbo-frame')) {
            form.setAttribute('data-turbo-frame', MODAL_FRAME);
        }
    });
}

function refreshMain(url) {
    const target = url || `${window.location.pathname}${window.location.search}`;
    visit(target, { frame: MAIN_FRAME, action: 'replace' });
}

function handleModalFrame(frame) {
    const state = isModalContent(frame);

    if (state === 'complete') {
        const complete = frame.querySelector('[data-admin-modal-complete]');
        const success = complete?.dataset.success;
        const error = complete?.dataset.error;
        const refreshUrl = complete?.dataset.refreshUrl;

        closeAdminModal();
        if (success) notifySuccess(success);
        if (error) notifyError(error);
        refreshMain(refreshUrl || `${window.location.pathname}${window.location.search}`);
        return;
    }

    if (state === 'form') {
        openAdminModal();
        wireModalCloseButtons(frame);
        initSweetAlert(frame);
        return;
    }

    closeAdminModal();
}

export function initAdminTurbo() {
    setConfirmMethod((message) =>
        confirmAction({
            title: 'Please confirm',
            text: message,
        }).then((result) => result.isConfirmed)
    );

    start();

    document.addEventListener('turbo:load', () => {
        markAdminLinks(document);
        wireModalCloseButtons(document);
        initSweetAlert(document);
    });

    document.addEventListener('turbo:frame-load', (event) => {
        const frame = event.target;
        markAdminLinks(frame);
        wireModalCloseButtons(frame);
        initSweetAlert(frame);

        if (frame.id === MODAL_FRAME) {
            handleModalFrame(frame);
        }

        if (frame.id === MAIN_FRAME) {
            showFlashFrom(frame);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.classList.contains('admin-modal-open')) {
            closeAdminModal();
        }
    });

    document.addEventListener('click', (event) => {
        const overlay = modalOverlay();
        if (overlay && event.target === overlay) {
            closeAdminModal();
        }
    });

    markAdminLinks(document);
    wireModalCloseButtons(document);
    initSweetAlert(document);
}

export { visit as TurboVisit };
