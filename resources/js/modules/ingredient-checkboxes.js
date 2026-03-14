/**
 * ingredient-checkboxes.js
 * Handles checkbox state for ingredient rows with localStorage persistence.
 */

document.addEventListener('DOMContentLoaded', function () {
    var checkboxes = document.querySelectorAll('.ingredient-checkbox');
    if (!checkboxes.length) return;

    var body = document.body;
    var recipeSlug = body.dataset.recipeSlug || 'recipe';
    var storageKey = 'ingredients-' + recipeSlug;

    // Load persisted state
    var saved = {};
    try {
        saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
    } catch (e) {
        saved = {};
    }

    checkboxes.forEach(function (checkbox, index) {
        var key = 'ing-' + index;

        // Restore saved state
        if (saved[key]) {
            checkbox.checked = true;
            applyStyle(checkbox, true);
        }

        checkbox.addEventListener('change', function () {
            var checked = this.checked;
            applyStyle(this, checked);

            // Persist state
            var state = {};
            try {
                state = JSON.parse(localStorage.getItem(storageKey) || '{}');
            } catch (e) {
                state = {};
            }
            state[key] = checked;
            localStorage.setItem(storageKey, JSON.stringify(state));
        });
    });

    function applyStyle(checkbox, checked) {
        var row = checkbox.closest('.ingredient-row');
        if (!row) return;
        if (checked) {
            row.classList.add('line-through', 'opacity-50');
        } else {
            row.classList.remove('line-through', 'opacity-50');
        }
    }
});
