/**
 * AI Batch Enhancement — Procesa múltiples recetas con IA en segundo plano
 */

const AiBatch = {
    batchUrl: null,
    progressUrl: null,
    pollInterval: null,
    batchId: null,

    init() {
        this.batchUrl     = document.querySelector('[data-ai-batch-url]')?.dataset.aiBatchUrl;
        this.progressUrl  = document.querySelector('[data-ai-batch-progress-url]')?.dataset.aiBatchProgressUrl;

        const btn = document.getElementById('ai-batch-btn');
        if (btn) {
            btn.addEventListener('click', () => this.startBatch());
        }
    },

    getSelectedIds() {
        return [...document.querySelectorAll('.recipe-checkbox:checked')]
            .map(el => parseInt(el.value))
            .filter(Boolean);
    },

    async startBatch() {
        const ids = this.getSelectedIds();
        if (ids.length === 0) {
            alert('Selecciona al menos una receta usando los checkboxes.');
            return;
        }

        if (!this.batchUrl) {
            alert('Error de configuración: URL del endpoint no encontrada. Recarga la página.');
            return;
        }

        const confirmed = confirm(
            `¿Mejorar ${ids.length} receta(s) con IA?\n\nLos campos SEO, historia y tips se actualizarán automáticamente sin revisión.`
        );
        if (!confirmed) return;

        this.showProgress(ids.length);

        try {
            const response = await fetch(this.batchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ ids }),
            });

            const data = await response.json();

            if (!response.ok) {
                this.showError(data.message || 'Error al iniciar el lote.');
                return;
            }

            this.batchId = data.batch_id;
            this.startPolling();

        } catch (err) {
            this.showError('Error de conexión: ' + err.message);
        }
    },

    startPolling() {
        this.pollInterval = setInterval(() => this.pollProgress(), 2000);
    },

    async pollProgress() {
        if (!this.batchId) return;

        try {
            const url = this.progressUrl.replace('__BATCH_ID__', this.batchId);
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            this.updateProgressUI(data);

            if (data.done) {
                clearInterval(this.pollInterval);
                this.onComplete(data);
            }
        } catch (err) {
            console.error('Error polling batch progress:', err);
        }
    },

    showProgress(total) {
        const panel = document.getElementById('ai-batch-panel');
        if (!panel) return;
        panel.classList.remove('hidden');
        document.getElementById('ai-batch-total').textContent = total;
        document.getElementById('ai-batch-processed').textContent = '0';
        document.getElementById('ai-batch-bar').style.width = '0%';
        document.getElementById('ai-batch-log').innerHTML = '';
        document.getElementById('ai-batch-status').textContent = 'Iniciando...';
    },

    updateProgressUI(data) {
        const pct = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;

        document.getElementById('ai-batch-processed').textContent = data.processed;
        document.getElementById('ai-batch-bar').style.width = pct + '%';
        document.getElementById('ai-batch-status').textContent = `${pct}% completado`;

        const logEl = document.getElementById('ai-batch-log');
        if (data.log?.length) {
            const last = data.log[data.log.length - 1];
            const color = last.status === 'error' ? 'text-red-400' : last.status === 'done' ? 'text-emerald-400' : 'text-zinc-400';
            logEl.innerHTML = `<span class="${color}">${last.time} — ${last.msg}</span>`;
        }
    },

    onComplete(data) {
        const errors = data.errors?.length ?? 0;
        const ok     = data.total - errors;
        document.getElementById('ai-batch-status').textContent =
            `✓ Completado: ${ok} mejoradas${errors > 0 ? `, ${errors} con error` : ''}.`;
        document.getElementById('ai-batch-bar').style.width = '100%';
        document.getElementById('ai-batch-bar').classList.add('bg-emerald-500');

        // Recargar la página después de 3 segundos para mostrar ai_enhanced_at actualizado
        setTimeout(() => window.location.reload(), 3000);
    },

    showError(msg) {
        const panel = document.getElementById('ai-batch-panel');
        if (panel) {
            panel.classList.remove('hidden');
            document.getElementById('ai-batch-status').textContent = '✗ ' + msg;
            document.getElementById('ai-batch-status').classList.add('text-red-400');
        }
    },
};

export default AiBatch;
