/**
 * import-progress.js
 * Polls the import progress endpoint every 2 seconds and updates the UI.
 * Expects a container with id="import-progress-container" and data-batch-id.
 */

document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('import-progress-container');
    if (!container) return;

    var batchId = container.dataset.batchId;
    if (!batchId) return;

    var progressBar = document.getElementById('import-progress-bar');
    var progressText = document.getElementById('import-progress-text');
    var progressPercent = document.getElementById('import-progress-percent');
    var statusBadge = document.getElementById('import-status-badge');
    var summary = document.getElementById('import-summary');
    var createdEl = document.getElementById('import-created');
    var updatedEl = document.getElementById('import-updated');
    var failedEl = document.getElementById('import-failed');

    var pollingInterval = null;
    var pollUrl = '/admin/recetas/importar/progreso/' + batchId;

    function poll() {
        fetch(pollUrl, {
            headers: { 'Accept': 'application/json' },
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            updateUI(data);

            // Stop polling on completion or failure
            if (data.status === 'finished' || data.status === 'failed' || data.percentage >= 100) {
                stopPolling();
                onComplete(data);
            }
        })
        .catch(function (err) {
            console.error('[ImportProgress] Poll error:', err);
            // Continue polling unless too many failures
        });
    }

    function updateUI(data) {
        var pct = Math.min(100, Math.round(data.percentage || 0));

        if (progressBar) {
            progressBar.style.width = pct + '%';
        }
        if (progressPercent) {
            progressPercent.textContent = pct + '%';
        }
        if (progressText) {
            progressText.textContent = data.message || ('Procesando... ' + (data.processed || 0) + '/' + (data.total || 0));
        }
    }

    function onComplete(data) {
        if (statusBadge) {
            statusBadge.textContent = data.status === 'failed' ? 'Error' : 'Completado';
            statusBadge.className = data.status === 'failed'
                ? 'text-xs px-2.5 py-1 rounded-full bg-red-900/50 text-red-300 border border-red-700/50'
                : 'text-xs px-2.5 py-1 rounded-full bg-emerald-900/50 text-emerald-300 border border-emerald-700/50';
        }

        if (progressBar) {
            progressBar.classList.remove('bg-amber-500');
            progressBar.classList.add(data.status === 'failed' ? 'bg-red-500' : 'bg-emerald-500');
        }

        if (summary) {
            summary.classList.remove('hidden');
        }

        if (createdEl && data.created !== undefined) {
            createdEl.textContent = data.created || 0;
        }
        if (updatedEl && data.updated !== undefined) {
            updatedEl.textContent = data.updated || 0;
        }
        if (failedEl && data.failed !== undefined) {
            failedEl.textContent = data.failed || 0;
        }
    }

    function startPolling() {
        poll(); // Immediate first poll
        pollingInterval = setInterval(poll, 2000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    startPolling();
});
