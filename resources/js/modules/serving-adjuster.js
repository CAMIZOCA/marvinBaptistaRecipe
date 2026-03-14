/**
 * serving-adjuster.js
 * Adjusts serving counts and scales ingredient amounts proportionally.
 */

document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('servings-adjuster');
    var minusBtn = document.getElementById('servings-minus');
    var plusBtn = document.getElementById('servings-plus');
    var display = document.getElementById('servings-display');

    if (!container || !minusBtn || !plusBtn || !display) return;

    var baseServings = parseInt(container.dataset.baseServings, 10) || 1;
    var currentServings = baseServings;

    minusBtn.addEventListener('click', function () {
        if (currentServings > 1) {
            currentServings--;
            update();
        }
    });

    plusBtn.addEventListener('click', function () {
        currentServings++;
        update();
    });

    function update() {
        display.textContent = currentServings;
        var ratio = currentServings / baseServings;

        document.querySelectorAll('.ingredient-amount').forEach(function (el) {
            var base = parseFloat(el.dataset.baseAmount);
            if (isNaN(base)) return;
            var scaled = base * ratio;
            el.textContent = formatAmount(scaled);
        });
    }

    /**
     * Format a decimal number as a fraction-friendly string.
     */
    function formatAmount(num) {
        // Common fractions
        var fractions = {
            0.25: '¼',
            0.33: '⅓',
            0.5:  '½',
            0.67: '⅔',
            0.75: '¾',
        };

        var whole = Math.floor(num);
        var decimal = Math.round((num - whole) * 100) / 100;

        var fracStr = '';
        var closestKey = null;
        var closestDiff = Infinity;
        Object.keys(fractions).forEach(function (key) {
            var diff = Math.abs(parseFloat(key) - decimal);
            if (diff < closestDiff) {
                closestDiff = diff;
                closestKey = parseFloat(key);
            }
        });

        if (closestDiff < 0.05 && closestKey !== null) {
            fracStr = fractions[String(closestKey)] || '';
        }

        if (whole === 0 && fracStr) {
            return fracStr;
        } else if (whole > 0 && fracStr) {
            return whole + ' ' + fracStr;
        } else {
            // Round to 2 decimals but trim trailing zeros
            return parseFloat(num.toFixed(2)).toString();
        }
    }
});
