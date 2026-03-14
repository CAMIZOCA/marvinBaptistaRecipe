/**
 * step-timer.js
 * Countdown timer for recipe steps with browser notification on completion.
 */

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.step-timer-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var totalSeconds = parseInt(this.dataset.duration, 10);
            if (isNaN(totalSeconds) || totalSeconds <= 0) return;

            var wrapper = this.parentNode;
            startTimer(wrapper, totalSeconds);
        });
    });
});

function startTimer(wrapper, totalSeconds) {
    var remaining = totalSeconds;

    // Request notification permission (non-blocking)
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Replace button with countdown display
    var display = document.createElement('div');
    display.className = 'flex items-center gap-3 px-4 py-2 bg-blue-100 text-blue-800 rounded-xl text-sm font-medium';
    display.innerHTML = [
        '<svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        '</svg>',
        '<span id="timer-countdown">' + formatTime(remaining) + '</span>',
        '<button type="button" id="timer-cancel" class="ml-2 text-blue-400 hover:text-blue-700 transition-colors text-xs" aria-label="Cancelar temporizador">✕ Cancelar</button>',
    ].join('');

    wrapper.innerHTML = '';
    wrapper.appendChild(display);

    var countdownEl = display.querySelector('#timer-countdown');
    var cancelBtn = display.querySelector('#timer-cancel');

    var intervalId = setInterval(function () {
        remaining--;

        if (remaining <= 0) {
            clearInterval(intervalId);
            display.classList.remove('bg-blue-100', 'text-blue-800');
            display.classList.add('bg-emerald-100', 'text-emerald-800');
            display.innerHTML = [
                '<svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">',
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>',
                '</svg>',
                '<span>¡Tiempo completado!</span>',
            ].join('');

            // Browser notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Marvin Baptista — Receta', {
                    body: '¡El temporizador ha terminado!',
                    icon: '/favicon.ico',
                });
            }

            // Audio beep (fallback)
            try {
                var ctx = new (window.AudioContext || window.webkitAudioContext)();
                var osc = ctx.createOscillator();
                osc.connect(ctx.destination);
                osc.frequency.value = 880;
                osc.start();
                osc.stop(ctx.currentTime + 0.5);
            } catch (e) {}

            return;
        }

        if (countdownEl) {
            countdownEl.textContent = formatTime(remaining);

            // Turn orange in last 30 seconds
            if (remaining <= 30) {
                display.classList.remove('bg-blue-100', 'text-blue-800');
                display.classList.add('bg-orange-100', 'text-orange-800');
            }
        }
    }, 1000);

    cancelBtn.addEventListener('click', function () {
        clearInterval(intervalId);
        display.remove();
        // Restore original button would require knowing duration; just show minimal reset
        var resetBtn = document.createElement('button');
        resetBtn.type = 'button';
        resetBtn.className = 'step-timer-btn inline-flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-sm font-medium transition-colors';
        resetBtn.dataset.duration = totalSeconds;
        resetBtn.innerHTML = [
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">',
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            '</svg>',
            formatTime(totalSeconds) + ' — Iniciar temporizador',
        ].join('');
        resetBtn.addEventListener('click', function () {
            startTimer(wrapper, totalSeconds);
        });
        wrapper.appendChild(resetBtn);
    });
}

function formatTime(seconds) {
    var m = Math.floor(seconds / 60);
    var s = seconds % 60;
    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
}
