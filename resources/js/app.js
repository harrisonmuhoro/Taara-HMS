import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// ─── Alpine.js Setup ───────────────────────────────────────────────────────
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

// ─── Toast Notification System ────────────────────────────────────────────
window.Toast = {
    container: null,

    init() {
        if (this.container) return;
        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        document.body.appendChild(this.container);
    },

    show(message, type = 'info', duration = 4000) {
        this.init();

        const icons = {
            success: `<svg class="w-5 h-5 shrink-0 toast-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>`,
            error:   `<svg class="w-5 h-5 shrink-0 toast-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>`,
            warning: `<svg class="w-5 h-5 shrink-0 toast-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                      </svg>`,
            info:    `<svg class="w-5 h-5 shrink-0 toast-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                      </svg>`,
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            ${icons[type] || icons.info}
            <span class="flex-1">${message}</span>
            <button onclick="this.closest('.toast').remove()" class="ml-2 opacity-40 hover:opacity-70 transition-opacity shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>`;

        this.container.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(8px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    success: (msg, d) => window.Toast.show(msg, 'success', d),
    error:   (msg, d) => window.Toast.show(msg, 'error',   d),
    warning: (msg, d) => window.Toast.show(msg, 'warning', d),
    info:    (msg, d) => window.Toast.show(msg, 'info',    d),
};

// ─── CSRF Helper for fetch() calls ────────────────────────────────────────
window.csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

window.http = {
    async post(url, data = {}) {
        const res = await fetch(url, {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     window.csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: 'Request failed' }));
            throw new Error(err.message || `HTTP ${res.status}`);
        }
        return res.json();
    },
    async get(url) {
        const res = await fetch(url, {
            headers: {
                'Accept':           'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    },
};

// ─── Global: auto-dismiss flash messages ──────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[role="alert"][data-autohide]').forEach(el => {
        const ms = parseInt(el.dataset.autohide, 10) || 5000;
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, ms);
    });
});
