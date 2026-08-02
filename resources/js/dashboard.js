// Shared UI helpers for the Taekwondo app shell (sidebar, dark mode, modal, toast)

// Mobile sidebar drawer toggle
window.toggleMenu = function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-overlay');

    if (!sidebar) return;

    if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.add('hidden');
        document.body.style.overflow = '';
        setTimeout(() => sidebar.classList.add('hidden'), 300);
    } else {
        sidebar.classList.remove('hidden');
        setTimeout(() => {
            sidebar.classList.add('open');
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }, 10);
    }
};

// Dark mode toggle
window.toggleDarkMode = function () {
    const html = document.documentElement;
    const isDark = !html.classList.contains('dark');

    html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateDarkModeUI(isDark);
};

window.updateDarkModeUI = function (isDark) {
    const icon = document.getElementById('darkModeIcon');
    const text = document.getElementById('darkModeText');
    const mobileIcon = document.getElementById('mobileDarkModeIcon');
    const desktopIcon = document.getElementById('desktopDarkModeIcon');
    const desktopText = document.getElementById('desktopDarkModeText');

    if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
    if (text) text.textContent = isDark ? 'Light Mode' : 'Dark Mode';
    if (mobileIcon) mobileIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
    if (desktopIcon) desktopIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
    if (desktopText) desktopText.textContent = isDark ? 'Light Mode' : 'Dark Mode';
};

// Generic modal open/close (.modal-overlay + .active pattern)
window.openModal = function (id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
};

window.closeModal = function (id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('active');
        document.body.style.overflow = '';
    }
};

// Fixed toast notification
window.showToast = function (title, message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;

    const iconContainer = document.getElementById('toastIconContainer');
    const icon = document.getElementById('toastIcon');
    const titleEl = document.getElementById('toastTitle');
    const msgEl = document.getElementById('toastMessage');

    titleEl.textContent = title;
    msgEl.textContent = message;

    iconContainer.className = 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0';
    if (type === 'success') {
        iconContainer.classList.add('bg-green-100', 'dark:bg-green-900/30', 'text-green-600', 'dark:text-green-400');
        icon.textContent = 'check_circle';
    } else if (type === 'error') {
        iconContainer.classList.add('bg-red-100', 'dark:bg-red-900/30', 'text-red-600', 'dark:text-red-400');
        icon.textContent = 'cancel';
    } else if (type === 'info') {
        iconContainer.classList.add('bg-brand-light', 'dark:bg-brand-blue/30', 'text-brand-blue', 'dark:text-brand-light');
        icon.textContent = 'info';
    }

    toast.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
};

// Custom stacked toast (toast-container)
window.showCustomMessage = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    let iconStr = 'check_circle';
    let colorClasses = 'border-green-100 dark:border-green-900/30 bg-white dark:bg-gray-800 text-green-600 dark:text-green-400';
    let bgOpacityClass = 'bg-green-100 dark:bg-green-900/30';

    if (type === 'error') {
        iconStr = 'error';
        colorClasses = 'border-red-100 dark:border-red-900/30 bg-white dark:bg-gray-800 text-red-500 dark:text-red-400';
        bgOpacityClass = 'bg-red-100 dark:bg-red-900/30';
    } else if (type === 'info') {
        iconStr = 'info';
        colorClasses = 'border-blue-100 dark:border-blue-900/30 bg-white dark:bg-gray-800 text-brand-blue dark:text-blue-400';
        bgOpacityClass = 'bg-blue-100 dark:bg-blue-900/30';
    }

    const toast = document.createElement('div');
    toast.className = `toast-enter flex items-center gap-3 px-5 py-4 rounded-2xl shadow-card border transition-colors max-w-sm ${colorClasses}`;
    toast.innerHTML = `
        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${bgOpacityClass}">
            <span class="material-symbols-outlined text-[20px]">${iconStr}</span>
        </div>
        <p class="text-sm font-bold text-gray-800 dark:text-white">${message}</p>
    `;

    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        toast.addEventListener('animationend', () => toast.remove());
    }, 3500);
};

// Password visibility toggle
window.togglePassword = function (inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
};

// Initialize dark mode UI icons on load
document.addEventListener('DOMContentLoaded', () => {
    updateDarkModeUI(document.documentElement.classList.contains('dark'));
});

// Global Livewire event listeners -> show toast notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', ({ title, message, type }) => {
        showToast(title || 'Notifikasi', message || '', type || 'success');
    });

    Livewire.on('notify', ({ message, type }) => {
        showToast('Notifikasi', message || '', type || 'success');
    });
});
