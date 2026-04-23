import './bootstrap.js';
import './styles/app.scss';

// Gestion du menu navbar mobile
document.addEventListener('DOMContentLoaded', () => {
    // Toggle menu mobile
    const navToggle = document.querySelector('.navbar__toggle');
    if (navToggle) {
        navToggle.addEventListener('click', function () {
            const collapse = document.getElementById('navCollapse');
            const isOpen = collapse.classList.toggle('active');
            this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            const navbar = document.querySelector('.navbar__container');
            if (navbar && !navbar.contains(e.target)) {
                const collapse = document.getElementById('navCollapse');
                const btn = document.querySelector('.navbar__toggle');
                if (collapse && collapse.classList.contains('active')) {
                    collapse.classList.remove('active');
                    btn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    // Activation des tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Activation des popovers Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Activation des dropdowns Bootstrap
    const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownTriggerList.map(function (dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });
});
