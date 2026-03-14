/**
 * mobile-nav.js
 * Handles mobile hamburger menu toggle, outside click, and escape key close.
 * Also handles the desktop recipes dropdown.
 */

document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initRecipesDropdown();
});

function initMobileMenu() {
    var btn = document.getElementById('mobile-menu-btn');
    var menu = document.getElementById('mobile-menu');
    var hamburgerIcon = document.getElementById('hamburger-icon');
    var closeIcon = document.getElementById('close-icon');

    if (!btn || !menu) return;

    var isOpen = false;

    function openMenu() {
        isOpen = true;
        menu.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
        if (hamburgerIcon) hamburgerIcon.classList.add('hidden');
        if (closeIcon) closeIcon.classList.remove('hidden');
    }

    function closeMenu() {
        isOpen = false;
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
        if (closeIcon) closeIcon.classList.add('hidden');
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (isOpen && !menu.contains(e.target) && !btn.contains(e.target)) {
            closeMenu();
        }
    });

    // Close on escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            closeMenu();
            btn.focus();
        }
    });
}

function initRecipesDropdown() {
    var wrapper = document.getElementById('recipes-dropdown-wrapper');
    var btn = document.getElementById('recipes-dropdown-btn');
    var dropdown = document.getElementById('recipes-dropdown');
    var icon = document.getElementById('recipes-dropdown-icon');

    if (!btn || !dropdown) return;

    var isOpen = false;

    function open() {
        isOpen = true;
        dropdown.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
        if (icon) icon.style.transform = 'rotate(180deg)';
    }

    function close() {
        isOpen = false;
        dropdown.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        if (icon) icon.style.transform = '';
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        isOpen ? close() : open();
    });

    document.addEventListener('click', function (e) {
        if (isOpen && wrapper && !wrapper.contains(e.target)) {
            close();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            close();
            btn.focus();
        }
    });
}
