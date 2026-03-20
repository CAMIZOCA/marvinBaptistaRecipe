/**
 * AI Batch Enhancement - Procesa multiples recetas con IA en segundo plano
 */

const AiBatch = {
    batchUrl: null,
    progressUrl: null,
    pollInterval: null,
    batchId: null,
    pendingLimit: 500,
    pendingCategorySlug: null,

    pollFailures: 0,
    maxPollFailures: 5,
    lastProcessed: 0,
    lastProgressAt: 0,
    batchStartedAt: 0,
    maxNoProgressMs: 3 * 60 * 1000,
    maxBatchMs: 2 * 60 * 60 * 1000,
    stalledWarnings: 0,
    maxStalledWarnings: 3,

    log(tag, payload) {
        try {
            console.info('[AI Batch][' + tag + ']', payload);
            if (payload !== undefined) {
                console.info('[AI Batch JSON][' + tag + ']\n' + JSON.stringify(payload, null, 2));
            }
        } catch (_) {}
    },

    init() {
        const configEl = document.querySelector('[data-ai-batch-url]');
        this.batchUrl = configEl?.dataset.aiBatchUrl;
        this.progressUrl = configEl?.dataset.aiBatchProgressUrl;
        this.pendingLimit = parseInt(configEl?.dataset.aiBatchPendingLimit || '500', 10) || 500;
        this.pendingCategorySlug = configEl?.dataset.aiBatchCategorySlug || null;

        const btn = document.getElementById('ai-batch-btn');
        if (btn) {
            btn.addEventListener('click', () => {
                // Defer to next tick so browser doesn't attribute confirm/dialog wait
                // time to the click handler itself.
                setTimeout(() => this.startBatch(), 0);
            });
        }

        const pendingBtn = document.getElementById('ai-batch-pending-btn');
        if (pendingBtn) {
            pendingBtn.addEventListener('click', () => {
                setTimeout(() => this.startPendingBatch(), 0);
            });
        }
    },

    getSelectedIds() {
        return [...document.querySelectorAll('.recipe-checkbox:checked')]
            .map(el => parseInt(el.value, 10))
            .filter(Boolean);
    },

    async startBatch() {
        const ids = this.getSelectedIds();
        if (ids.length === 0) {
            alert('Selecciona al menos una receta usando los checkboxes.');
            return;
        }

        if (!this.batchUrl) {
            alert('Error de configuracion: URL del endpoint no encontrada. Recarga la pagina.');
            return;
        }

        const confirmed = confirm(
            `¿Mejorar ${ids.length} receta(s) con IA?\n\nLos campos SEO, historia y tips se actualizaran automaticamente sin revision.`
        );
        if (!confirmed) return;

        await this.requestStart({ ids }, ids.length);
    },

    async startPendingBatch() {
        if (!this.batchUrl) {
            alert('Error de configuracion: URL del endpoint no encontrada. Recarga la pagina.');
            return;
        }

        const input = prompt(
            '¿Cuantas recetas pendientes deseas procesar?\n\nIngresa un numero (ej: 100).\nUsa 0 para procesar TODAS las pendientes.',
            String(this.pendingLimit)
        );

        if (input === null) {
            return; // usuario cancelo
        }

        const parsedLimit = Number.parseInt(String(input).trim(), 10);
        if (Number.isNaN(parsedLimit) || parsedLimit < 0) {
            alert('Valor invalido. Debes ingresar un numero mayor o igual a 0.');
            return;
        }

        const runLabel = parsedLimit === 0
            ? 'TODAS las recetas pendientes'
            : `hasta ${parsedLimit} receta(s) pendientes`;

        const confirmed = confirm(
            `¿Procesar ${runLabel} con el prompt automatico?`
        );
        if (!confirmed) return;

        const payload = {
            mode: 'pending',
            limit: parsedLimit,
        };

        if (this.pendingCategorySlug) {
            payload.category_slug = this.pendingCategorySlug;
        }

        await this.requestStart(payload, parsedLimit === 0 ? 0 : parsedLimit);
    },

    async requestStart(payload, expectedTotal) {
        this.log('REQUEST start', { url: this.batchUrl, payload });
        this.setButtonsBusy(true);
        this.showProgress(expectedTotal);

        try {
            const response = await fetch(this.batchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await this.readJsonSafe(response);
            this.log('RESPONSE start', { status: response.status, ok: response.ok, data });

            if (!response.ok) {
                const msg = data?.error || data?.message || 'Error al iniciar el lote.';
                this.showError(msg);
                this.setButtonsBusy(false);
                return;
            }

            const total = parseInt(data?.total || expectedTotal || '0', 10);
            if (total > 0) {
                document.getElementById('ai-batch-total').textContent = String(total);
            }

            if (Array.isArray(data?.recipes) && data.recipes.length > 0) {
                const preview = data.recipes
                    .slice(0, 5)
                    .map((r) => `#${r.id} ${r.title}`)
                    .join(' | ');
                const suffix = data.recipes.length > 5 ? ' | ...' : '';
                this.showInfo(`Recetas en lote: ${preview}${suffix}`);
            }

            if (data?.warning) {
                this.showWarning(data.warning);
            }

            this.batchId = data.batch_id;
            this.startPolling();
        } catch (err) {
            this.showError('Error de conexion al iniciar lote: ' + (err?.message || 'desconocido'));
            this.setButtonsBusy(false);
        }
    },

    startPolling() {
        this.pollFailures = 0;
        this.lastProcessed = 0;
        this.lastProgressAt = Date.now();
        this.batchStartedAt = Date.now();
        this.stalledWarnings = 0;

        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }

        this.pollInterval = setInterval(() => this.pollProgress(), 2000);
    },

    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    },

    async pollProgress() {
        if (!this.batchId || !this.progressUrl) return;

        if (Date.now() - this.batchStartedAt > this.maxBatchMs) {
            this.showError('Proceso detenido por tiempo maximo. Verifica cola/worker y reintenta.');
            this.stopPolling();
            this.setButtonsBusy(false);
            return;
        }

        try {
            const url = this.progressUrl.replace('__BATCH_ID__', this.batchId);
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' },
            });

            const data = await this.readJsonSafe(response);
            this.log('RESPONSE progress', { status: response.status, ok: response.ok, batchId: this.batchId, data });

            if (!response.ok) {
                this.pollFailures++;

                if (response.status === 404) {
                    this.showError('Lote no encontrado o expirado. Si fue un proceso largo, vuelve a iniciarlo.');
                    this.stopPolling();
                    this.setButtonsBusy(false);
                    return;
                }

                if (this.pollFailures >= this.maxPollFailures) {
                    this.showError('No se pudo consultar el progreso varias veces seguidas. Revisa conexion y estado del servidor.');
                    this.stopPolling();
                    this.setButtonsBusy(false);
                }

                return;
            }

            this.pollFailures = 0;
            this.updateProgressUI(data);

            if (typeof data.processed === 'number' && data.processed > this.lastProcessed) {
                this.lastProcessed = data.processed;
                this.lastProgressAt = Date.now();
                this.stalledWarnings = 0;
            } else if (Date.now() - this.lastProgressAt > this.maxNoProgressMs && !data.done) {
                this.stalledWarnings++;
                this.showWarning('El lote sigue corriendo pero no avanza hace varios minutos. Verifica queue worker y limites del proveedor IA.');

                if (this.stalledWarnings >= this.maxStalledWarnings) {
                    this.showError('Proceso detenido por falta de avance. Revisa worker/cola, valida proveedor IA y vuelve a iniciar el lote.');
                    this.stopPolling();
                    this.setButtonsBusy(false);
                    return;
                }

                this.lastProgressAt = Date.now();
            }

            if (data.done) {
                this.stopPolling();
                this.onComplete(data);
            }
        } catch (err) {
            this.pollFailures++;
            if (this.pollFailures >= this.maxPollFailures) {
                this.showError('Error de conexion persistente consultando progreso.');
                this.stopPolling();
                this.setButtonsBusy(false);
            }
        }
    },

    showProgress(total) {
        const panel = document.getElementById('ai-batch-panel');
        if (!panel) return;

        panel.classList.remove('hidden');
        document.getElementById('ai-batch-total').textContent = String(total || 0);
        document.getElementById('ai-batch-processed').textContent = '0';
        document.getElementById('ai-batch-bar').style.width = '0%';
        document.getElementById('ai-batch-log').innerHTML = '';

        const statusEl = document.getElementById('ai-batch-status');
        statusEl.textContent = 'Iniciando...';
        statusEl.classList.remove('text-red-400', 'text-amber-400');
        statusEl.classList.add('text-zinc-400');
    },

    updateProgressUI(data) {
        const total = Number(data?.total || 0);
        const processed = Number(data?.processed || 0);
        const pct = total > 0 ? Math.round((processed / total) * 100) : 0;

        document.getElementById('ai-batch-processed').textContent = String(processed);
        document.getElementById('ai-batch-bar').style.width = pct + '%';

        const statusEl = document.getElementById('ai-batch-status');
        statusEl.textContent = `${pct}% completado`;
        statusEl.classList.remove('text-red-400', 'text-amber-400');
        statusEl.classList.add('text-zinc-400');

        const logEl = document.getElementById('ai-batch-log');
        if (Array.isArray(data?.log) && data.log.length > 0) {
            const last = data.log[data.log.length - 1];
            const color = last.status === 'error'
                ? 'text-red-400'
                : (last.status === 'done' ? 'text-emerald-400' : 'text-zinc-400');
            logEl.innerHTML = `<span class="${color}">${last.time} - ${last.msg}</span>`;
        }
    },

    onComplete(data) {
        const errors = Array.isArray(data?.errors) ? data.errors.length : 0;
        const total = Number(data?.total || 0);
        const ok = Math.max(total - errors, 0);

        const statusEl = document.getElementById('ai-batch-status');
        statusEl.textContent = `OK: ${ok} mejoradas${errors > 0 ? `, ${errors} con error` : ''}.`;
        statusEl.classList.remove('text-zinc-400', 'text-red-400', 'text-amber-400');
        statusEl.classList.add(errors > 0 ? 'text-amber-400' : 'text-emerald-400');

        document.getElementById('ai-batch-bar').style.width = '100%';
        document.getElementById('ai-batch-bar').classList.add('bg-emerald-500');

        this.setButtonsBusy(false);

        setTimeout(() => window.location.reload(), 3000);
    },

    showWarning(msg) {
        const statusEl = document.getElementById('ai-batch-status');
        if (!statusEl) return;

        statusEl.textContent = 'ADVERTENCIA: ' + msg;
        statusEl.classList.remove('text-zinc-400', 'text-red-400');
        statusEl.classList.add('text-amber-400');
    },

    showInfo(msg) {
        const logEl = document.getElementById('ai-batch-log');
        if (!logEl) return;
        logEl.innerHTML = `<span class="text-zinc-400">${msg}</span>`;
    },

    showError(msg) {
        const panel = document.getElementById('ai-batch-panel');
        if (!panel) return;

        panel.classList.remove('hidden');
        const statusEl = document.getElementById('ai-batch-status');
        statusEl.textContent = 'ERROR: ' + msg;
        statusEl.classList.remove('text-zinc-400', 'text-amber-400');
        statusEl.classList.add('text-red-400');
    },

    setButtonsBusy(isBusy) {
        ['ai-batch-btn', 'ai-batch-pending-btn'].forEach((id) => {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.disabled = isBusy;
            btn.classList.toggle('opacity-60', isBusy);
            btn.classList.toggle('cursor-not-allowed', isBusy);
        });
    },

    async readJsonSafe(response) {
        try {
            return await response.json();
        } catch (_) {
            return null;
        }
    },
};

export default AiBatch;
