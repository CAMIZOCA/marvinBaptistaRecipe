/**
 * ai-enhance.js — AI Enhancement workflow
 * 1. "Verificar conexión" → POST test endpoint → show status dot
 * 2. "Mejorar con IA" → pre-flight check → POST enhance URL → diff view
 * 3. Accept checkboxes → POST save URL → reload
 */

document.addEventListener('DOMContentLoaded', function () {
    var enhanceBtn    = document.getElementById('btn-ai-enhance');
    var checkBtn      = document.getElementById('btn-ai-check');
    var saveBtn       = document.getElementById('btn-ai-save');
    var spinner       = document.getElementById('ai-enhance-spinner');
    var btnText       = document.getElementById('ai-enhance-text');
    var diffContainer = document.getElementById('ai-diff-container');
    var diffFields    = document.getElementById('ai-diff-fields');
    var statusDot     = document.getElementById('ai-status-dot');
    var statusText    = document.getElementById('ai-status-text');
    var errorBox      = document.getElementById('ai-error-box');
    var errorTitle    = document.getElementById('ai-error-title');
    var errorDetail   = document.getElementById('ai-error-detail');
    var errorLink     = document.getElementById('ai-error-link');

    if (!enhanceBtn) return;

    var pageData   = document.querySelector('[data-enhance-url]');
    var enhanceUrl = enhanceBtn.dataset.enhanceUrl || (pageData && pageData.dataset.enhanceUrl) || '';
    var fieldUrl   = (pageData && pageData.dataset.fieldUrl) || '';
    var saveUrl    = (saveBtn && saveBtn.dataset.saveUrl) || (pageData && pageData.dataset.saveUrl) || '';
    var testUrl    = (checkBtn && checkBtn.dataset.testUrl) || '';
    var promptUrl  = (pageData && pageData.dataset.promptUrl) || '';

    // Store full suggested object for save step
    var _suggested = {};

    // ── Copy-prompt modal elements ──────────────────────────────────
    var promptModal            = document.getElementById('prompt-modal');
    var promptModalClose       = document.getElementById('prompt-modal-close');
    var promptModalCloseFooter = document.getElementById('prompt-modal-close-footer');
    var promptModalLoading     = document.getElementById('prompt-modal-loading');
    var promptModalContent     = document.getElementById('prompt-modal-content');
    var promptModalFooter      = document.getElementById('prompt-modal-footer');
    var promptTextArea         = document.getElementById('prompt-text-area');
    var promptResponseArea     = document.getElementById('prompt-response-area');
    var promptParseError       = document.getElementById('prompt-parse-error');
    var clipboardCopyText      = document.getElementById('clipboard-copy-text');
    var btnCopyPrompt          = document.getElementById('btn-copy-prompt');
    var btnClipboardCopy       = document.getElementById('btn-clipboard-copy');
    var btnApplyResponse       = document.getElementById('btn-apply-response');
    // Holds current field values returned by the /prompt endpoint for the diff view
    var _promptCurrent         = {};

    // Fields dispatched as separate AI calls (order = display order in progress panel)
    var FIELDS = [
        { id: 'seo_title',                 label: 'SEO Title'               },
        { id: 'seo_description',           label: 'SEO Description'         },
        { id: 'story',                     label: 'Historia / Origen'       },
        { id: 'tips_secrets',              label: 'Trucos y Secretos'       },
        { id: 'faq',                       label: 'Preguntas Frecuentes'    },
        { id: 'amazon_keywords',           label: 'Keywords Amazon'         },
        { id: 'internal_link_suggestions', label: 'Sugerencias de Links'    },
    ];

    /* ─── Status helpers ──────────────────────────────────────── */

    function setStatus(state, message) {
        var dotClasses = {
            idle:     'bg-zinc-500',
            checking: 'bg-yellow-400 animate-pulse',
            ok:       'bg-emerald-400',
            error:    'bg-red-400',
            warning:  'bg-amber-400',
        };
        if (statusDot) {
            statusDot.className = 'w-2 h-2 rounded-full shrink-0 ' + (dotClasses[state] || dotClasses.idle);
        }
        if (statusText) statusText.textContent = message || '';
    }

    function showError(title, detail, showSettingsLink) {
        if (!errorBox) return;
        errorBox.classList.remove('hidden');
        errorBox.className = 'rounded-lg border border-red-800/60 bg-red-950/40 px-3 py-2.5 text-xs space-y-1';
        if (errorTitle)  { errorTitle.className  = 'font-semibold text-red-300';    errorTitle.textContent  = title  || ''; }
        if (errorDetail) { errorDetail.className = 'leading-relaxed text-red-200/80'; errorDetail.textContent = detail || ''; }
        if (errorLink) {
            if (showSettingsLink) {
                errorLink.classList.remove('hidden');
                errorLink.className = 'underline underline-offset-2 font-medium text-red-300 hover:text-red-200';
            } else {
                errorLink.classList.add('hidden');
            }
        }
        if (errorBox.scrollIntoView) errorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function showInfo(title, detail) {
        if (!errorBox) return;
        errorBox.classList.remove('hidden');
        errorBox.className = 'rounded-lg border border-amber-800/60 bg-amber-950/40 px-3 py-2.5 text-xs space-y-1';
        if (errorTitle)  { errorTitle.className  = 'font-semibold text-amber-300';    errorTitle.textContent  = title  || ''; }
        if (errorDetail) { errorDetail.className = 'leading-relaxed text-amber-200/80'; errorDetail.textContent = detail || ''; }
        if (errorLink) errorLink.classList.add('hidden');
    }

    function clearError() {
        if (!errorBox) return;
        errorBox.classList.add('hidden');
        if (errorTitle)  errorTitle.textContent  = '';
        if (errorDetail) errorDetail.textContent = '';
    }

    /* ─── Copy-prompt modal helpers ───────────────────────────── */

    function openPromptModal() {
        if (!promptModal) return;
        promptModal.classList.remove('hidden');
        promptModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePromptModal() {
        if (!promptModal) return;
        promptModal.classList.add('hidden');
        promptModal.classList.remove('flex');
        document.body.style.overflow = '';
        // Reset state for next open
        if (promptModalLoading) promptModalLoading.classList.remove('hidden');
        if (promptModalContent) promptModalContent.classList.add('hidden');
        if (promptModalFooter)  promptModalFooter.classList.add('hidden');
        if (promptTextArea)     promptTextArea.value = '';
        if (promptResponseArea) promptResponseArea.value = '';
        if (promptParseError)   { promptParseError.classList.add('hidden'); promptParseError.textContent = ''; }
        if (clipboardCopyText)  clipboardCopyText.textContent = 'Copiar al portapapeles';
        if (btnClipboardCopy) {
            btnClipboardCopy.classList.remove('bg-emerald-700', 'hover:bg-emerald-600');
            btnClipboardCopy.classList.add('bg-violet-700', 'hover:bg-violet-600');
        }
    }

    /* ─── Connection check ────────────────────────────────────── */

    function checkConnection(silent) {
        if (!testUrl) {
            setStatus('warning', 'URL de prueba no configurada');
            return Promise.resolve(null); // null = unknown / skip
        }

        setStatus('checking', 'Verificando conexión...');
        if (!silent) clearError();

        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        return fetch(testUrl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body:    JSON.stringify({}),
        })
        .then(function (res) { return res.json().then(function (d) { return { httpOk: res.ok, data: d }; }); })
        .then(function (r) {
            if (r.data.ok) {
                setStatus('ok', r.data.message || 'Conectado');
                if (!silent) clearError();
                return true;
            } else {
                setStatus('error', 'Sin conexión con la IA');
                if (!silent) {
                    showError(
                        'No se pudo conectar con la IA',
                        r.data.message || 'Revisa la configuración de tu proveedor de IA.',
                        true
                    );
                }
                return false;
            }
        })
        .catch(function (err) {
            setStatus('error', 'Error de red');
            if (!silent) {
                showError(
                    'Error de red al verificar la conexión',
                    err.message || 'Comprueba tu conexión a internet y que el servidor esté activo.',
                    false
                );
            }
            return false;
        });
    }

    if (checkBtn) {
        checkBtn.addEventListener('click', function () {
            checkBtn.disabled = true;
            checkConnection(false).finally(function () { checkBtn.disabled = false; });
        });
    }

    /* ─── Enhance button ──────────────────────────────────────── */

    enhanceBtn.addEventListener('click', function () {
        if (!enhanceUrl) {
            showError('URL de mejora no configurada', 'Recarga la página e inténtalo de nuevo.', false);
            return;
        }

        enhanceBtn.disabled = true;
        if (spinner)       spinner.classList.remove('hidden');
        if (btnText)       btnText.textContent = 'Verificando conexión...';
        if (diffContainer) diffContainer.classList.add('hidden');
        clearError();

        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        // Step 1: pre-flight connection check (silent mode)
        checkConnection(true).then(function (connected) {

            // connected === null means testUrl not available — proceed anyway
            if (connected === false) {
                showError(
                    'La IA no está disponible',
                    'Verifica que el proveedor esté configurado correctamente en Ajustes → IA antes de continuar.',
                    true
                );
                resetEnhanceBtn();
                return;
            }

            // Step 2: process fields sequentially — local LLMs handle one request at a time
            if (btnText) btnText.textContent = 'Generando campos...';
            showProgressPanel();

            var collectedSuggested = {};
            var collectedCurrent   = {};

            (async function () {
                for (var i = 0; i < FIELDS.length; i++) {
                    var f = FIELDS[i];
                    if (btnText) btnText.textContent = 'Campo ' + (i + 1) + '/' + FIELDS.length + ': ' + f.label + '…';
                    try {
                        var res  = await fetch(fieldUrl, {
                            method:  'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body:    JSON.stringify({ field: f.id }),
                        });
                        var data = await res.json();
                        if (res.ok && data.success) {
                            collectedSuggested[data.field] = data.value;
                            collectedCurrent[data.field]   = data.current;
                            updateFieldProgress(f.id, 'ok');
                        } else {
                            updateFieldProgress(f.id, 'error', data.error || data.message || 'Error desconocido');
                        }
                    } catch (err) {
                        updateFieldProgress(f.id, 'error', err.message);
                    }
                }

                var ok = Object.keys(collectedSuggested).length;
                if (ok === 0) {
                    setStatus('error', 'No se generó ningún campo');
                    showError(
                        'No se pudo generar ningún campo',
                        'Revisa los errores en la lista. Verifica que el modelo esté activo y el timeout sea suficiente.',
                        false
                    );
                    resetEnhanceBtn();
                    return;
                }
                setStatus('ok', ok + '/' + FIELDS.length + ' campos generados ✓');
                _suggested = collectedSuggested;
                renderDiffView(collectedSuggested, collectedCurrent);
                resetEnhanceBtn();
            })();
        });
    });

    function resetEnhanceBtn() {
        enhanceBtn.disabled = false;
        if (spinner) spinner.classList.add('hidden');
        if (btnText) btnText.textContent = 'Mejorar con IA';
    }

    /* ─── Save button ─────────────────────────────────────────── */

    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            if (!saveUrl) {
                showError('URL de guardado no configurada', 'Recarga la página e inténtalo de nuevo.', false);
                return;
            }

            var fields = {};
            document.querySelectorAll('.ai-field-accept:checked').forEach(function (cb) {
                var f = cb.dataset.field;
                if (_suggested[f] !== undefined) fields[f] = _suggested[f];
            });

            if (Object.keys(fields).length === 0) {
                showInfo('Nada seleccionado', 'Activa al menos un campo con el checkbox "Aceptar" antes de guardar.');
                return;
            }

            saveBtn.disabled    = true;
            saveBtn.textContent = 'Guardando...';
            clearError();

            var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

            fetch(saveUrl, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body:    JSON.stringify({ fields: fields }),
            })
            .then(function (res) {
                if (!res.ok) return res.json().then(function (d) { throw new Error(d.error || 'Error al guardar'); });
                return res.json();
            })
            .then(function () {
                saveBtn.textContent = '¡Guardado!';
                saveBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
                saveBtn.classList.add('bg-blue-600');
                setTimeout(function () { window.location.reload(); }, 900);
            })
            .catch(function (err) {
                showError('Error al guardar', err.message, false);
                saveBtn.disabled    = false;
                saveBtn.textContent = 'Guardar Cambios Aceptados';
            });
        });
    }

    /* ─── Per-field progress panel ────────────────────────────── */

    function showProgressPanel() {
        if (!diffFields || !diffContainer) return;
        diffFields.innerHTML = FIELDS.map(function (f) {
            return '<div class="flex items-center justify-between px-4 py-2.5 bg-zinc-800/60 rounded-xl border border-zinc-700/60">'
                + '<span class="text-xs font-medium text-zinc-300">' + esc(f.label) + '</span>'
                + '<span id="ai-prog-' + f.id + '" class="text-xs text-zinc-500 flex items-center gap-1.5 shrink-0">'
                + '<svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">'
                + '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>'
                + '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>'
                + '</svg>Generando…</span>'
                + '</div>';
        }).join('');
        diffContainer.classList.remove('hidden');
        diffContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function updateFieldProgress(fieldId, state, errorMsg) {
        var el = document.getElementById('ai-prog-' + fieldId);
        if (!el) return;
        if (state === 'ok') {
            el.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
                + '<span class="text-emerald-400">Listo</span>';
        } else {
            var short = (errorMsg || 'Error').substring(0, 55);
            el.innerHTML = '<svg class="w-3.5 h-3.5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">'
                + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'
                + '<span class="text-red-400 max-w-[180px] truncate" title="' + esc(errorMsg || '') + '">' + esc(short) + '</span>';
        }
    }

    /* ─── SEO Analyzer bridge ─────────────────────────────────── */

    // Maps AI field names to the live form input selectors watched by seo-analyzer.js
    var FIELD_DOM_MAP = {
        seo_title:       '#seo_title',
        seo_description: '#seo_description',
    };

    function applyToDOM(field, value) {
        var sel = FIELD_DOM_MAP[field];
        if (!sel) return;
        var el = document.querySelector(sel);
        if (!el) return;
        el.value = value || '';
        el.dispatchEvent(new Event('input', { bubbles: true }));
    }

    /* ─── Render diff view ────────────────────────────────────── */

    function renderDiffView(suggested, current) {
        if (!diffFields || !diffContainer) return;
        diffFields.innerHTML = '';

        var fieldConfig = {
            seo_title:                  { label: 'SEO Title',            type: 'text',     maxLen: 60  },
            seo_description:            { label: 'SEO Description',      type: 'text',     maxLen: 160 },
            story:                      { label: 'Historia / Origen',    type: 'longtext'              },
            tips_secrets:               { label: 'Trucos y Secretos',    type: 'tips'                  },
            faq:                        { label: 'Preguntas Frecuentes', type: 'faq'                   },
            amazon_keywords:            { label: 'Keywords Amazon',      type: 'list'                  },
            internal_link_suggestions:  { label: 'Sugerencias de Links', type: 'list'                  },
        };

        var keys = Object.keys(suggested).filter(function (k) { return fieldConfig[k]; });

        if (keys.length === 0) {
            diffFields.innerHTML = '<p class="text-zinc-400 text-sm">La IA no generó sugerencias para esta receta.</p>';
            diffContainer.classList.remove('hidden');
            return;
        }

        keys.forEach(function (field) {
            var cfg  = fieldConfig[field];
            var val  = suggested[field];
            var prev = current[field];

            var item = document.createElement('div');
            item.className = 'bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden';

            // ── Header ──
            var header = document.createElement('div');
            header.className = 'flex items-center justify-between px-4 py-3 bg-zinc-700/50 border-b border-zinc-700';
            header.innerHTML =
                '<span class="text-xs font-bold text-zinc-200 uppercase tracking-wider">' + esc(cfg.label) + '</span>' +
                '<label class="flex items-center gap-2 cursor-pointer select-none">' +
                '<input type="checkbox" class="ai-field-accept w-4 h-4 rounded border-zinc-500 bg-zinc-700 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-zinc-800 cursor-pointer" data-field="' + esc(field) + '" checked>' +
                '<span class="text-xs font-medium text-zinc-300">Aceptar</span>' +
                '</label>';
            item.appendChild(header);

            // Real-time SEO preview: toggle the live form field when the checkbox changes
            var cb = header.querySelector('.ai-field-accept');
            if (cb && FIELD_DOM_MAP[field]) {
                cb.addEventListener('change', function () {
                    applyToDOM(field, this.checked ? (suggested[field] || '') : (current[field] || ''));
                });
            }

            var body = document.createElement('div');
            body.className = 'p-4 space-y-3';

            // ── Current (only for text/longtext) ──
            if ((cfg.type === 'text' || cfg.type === 'longtext') && prev) {
                var curBlock = document.createElement('div');
                curBlock.className = 'space-y-1';
                curBlock.innerHTML =
                    '<p class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">Actual</p>' +
                    '<p class="text-xs text-zinc-400 bg-zinc-900/50 rounded-lg px-3 py-2 leading-relaxed line-clamp-3">' + esc(String(prev)) + '</p>';
                body.appendChild(curBlock);
            }

            // ── Suggested ──
            var sugBlock = document.createElement('div');
            sugBlock.className = 'space-y-1';
            sugBlock.innerHTML = '<p class="text-[10px] font-semibold text-emerald-500 uppercase tracking-wider">Sugerido por IA</p>';

            if (cfg.type === 'text') {
                var lenInfo = cfg.maxLen ? ' <span class="text-zinc-500">(' + String(val).length + '/' + cfg.maxLen + ' chars)</span>' : '';
                sugBlock.innerHTML +=
                    '<p class="text-xs text-emerald-200 bg-emerald-950/50 border border-emerald-800/40 rounded-lg px-3 py-2 leading-relaxed">' +
                    esc(String(val)) + lenInfo + '</p>';

            } else if (cfg.type === 'longtext') {
                sugBlock.innerHTML +=
                    '<p class="text-xs text-emerald-200 bg-emerald-950/50 border border-emerald-800/40 rounded-lg px-3 py-2 leading-relaxed whitespace-pre-wrap max-h-48 overflow-y-auto">' +
                    esc(String(val)) + '</p>';

            } else if (cfg.type === 'tips') {
                var tipsHtml = '<div class="text-xs text-emerald-200 bg-emerald-950/50 border border-emerald-800/40 rounded-lg px-3 py-2 space-y-1 max-h-48 overflow-y-auto">';
                if (Array.isArray(val)) {
                    val.forEach(function (tip, i) {
                        var tipText = typeof tip === 'object' ? (tip.tip || tip.text || JSON.stringify(tip)) : String(tip);
                        tipsHtml += '<p class="leading-relaxed"><span class="text-emerald-400 font-bold">' + (i+1) + '.</span> ' + esc(tipText) + '</p>';
                    });
                } else {
                    tipsHtml += '<p class="leading-relaxed whitespace-pre-wrap">' + esc(String(val)) + '</p>';
                }
                tipsHtml += '</div>';
                sugBlock.innerHTML += tipsHtml;

            } else if (cfg.type === 'faq') {
                var faqHtml = '<div class="space-y-2 max-h-56 overflow-y-auto">';
                (Array.isArray(val) ? val : []).forEach(function (faq, i) {
                    faqHtml +=
                        '<div class="text-xs bg-emerald-950/50 border border-emerald-800/40 rounded-lg px-3 py-2">' +
                        '<p class="font-semibold text-emerald-300 mb-1">Q' + (i+1) + ': ' + esc(faq.question || '') + '</p>' +
                        '<p class="text-emerald-200 leading-relaxed">' + esc(faq.answer || '') + '</p>' +
                        '</div>';
                });
                faqHtml += '</div>';
                sugBlock.innerHTML += faqHtml;

            } else if (cfg.type === 'list') {
                var listHtml = '<ul class="text-xs text-emerald-200 bg-emerald-950/50 border border-emerald-800/40 rounded-lg px-4 py-2 space-y-1 list-disc">';
                (Array.isArray(val) ? val : [val]).forEach(function (listItem) {
                    listHtml += '<li class="leading-relaxed">' + esc(String(listItem)) + '</li>';
                });
                listHtml += '</ul>';
                sugBlock.innerHTML += listHtml;
            }

            body.appendChild(sugBlock);
            item.appendChild(body);
            diffFields.appendChild(item);
        });

        diffContainer.classList.remove('hidden');
        diffContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Push suggested values into the live form fields so the SEO Analyzer updates immediately
        Object.keys(FIELD_DOM_MAP).forEach(function (field) {
            if (suggested[field] !== undefined) {
                applyToDOM(field, suggested[field]);
            }
        });
    }

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    /* ─── Copy-prompt modal: event listeners ──────────────────── */

    // Open modal and fetch prompt from server
    if (btnCopyPrompt) {
        btnCopyPrompt.addEventListener('click', function () {
            if (!promptUrl) {
                showError('URL de prompt no configurada', 'Recarga la página e inténtalo de nuevo.', false);
                return;
            }
            openPromptModal();
            var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
            fetch(promptUrl, {
                method:  'GET',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                _promptCurrent = data.current || {};
                if (promptTextArea)     promptTextArea.value = data.prompt || '';
                if (promptModalLoading) promptModalLoading.classList.add('hidden');
                if (promptModalContent) promptModalContent.classList.remove('hidden');
                if (promptModalFooter)  promptModalFooter.classList.remove('hidden');
            })
            .catch(function (err) {
                closePromptModal();
                showError('No se pudo construir el prompt', err.message, false);
            });
        });
    }

    // Close via X / Cancelar button
    if (promptModalClose)       promptModalClose.addEventListener('click', closePromptModal);
    if (promptModalCloseFooter) promptModalCloseFooter.addEventListener('click', closePromptModal);

    // Close on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && promptModal && !promptModal.classList.contains('hidden')) {
            closePromptModal();
        }
    });

    // Close on backdrop click
    if (promptModal) {
        promptModal.addEventListener('click', function (e) {
            if (e.target === promptModal) closePromptModal();
        });
    }

    // Copy to clipboard
    if (btnClipboardCopy) {
        btnClipboardCopy.addEventListener('click', function () {
            if (!promptTextArea || !promptTextArea.value) return;
            var setOk = function () {
                if (clipboardCopyText) clipboardCopyText.textContent = '¡Copiado!';
                btnClipboardCopy.classList.remove('bg-violet-700', 'hover:bg-violet-600');
                btnClipboardCopy.classList.add('bg-emerald-700', 'hover:bg-emerald-600');
                setTimeout(function () {
                    if (clipboardCopyText) clipboardCopyText.textContent = 'Copiar al portapapeles';
                    btnClipboardCopy.classList.remove('bg-emerald-700', 'hover:bg-emerald-600');
                    btnClipboardCopy.classList.add('bg-violet-700', 'hover:bg-violet-600');
                }, 2000);
            };
            // Clipboard API requires HTTPS — fall back to execCommand on HTTP (local .test domains)
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(promptTextArea.value)
                    .then(setOk)
                    .catch(function () {
                        promptTextArea.select();
                        document.execCommand('copy');
                        setOk();
                    });
            } else {
                promptTextArea.select();
                document.execCommand('copy');
                setOk();
            }
        });
    }

    // Apply pasted JSON response → renderDiffView (reuses the existing save flow)
    if (btnApplyResponse) {
        btnApplyResponse.addEventListener('click', function () {
            var raw = (promptResponseArea ? promptResponseArea.value : '').trim();

            var showParseErr = function (msg) {
                if (promptParseError) {
                    promptParseError.textContent = msg;
                    promptParseError.classList.remove('hidden');
                }
            };

            if (!raw) {
                showParseErr('Pega la respuesta JSON de la IA antes de aplicar.');
                return;
            }

            // Extract JSON — handles raw JSON, ```json fences, and leading/trailing text
            // (mirrors server-side extractJsonFromResponse logic)
            var jsonStr = raw;
            var fence = raw.match(/```(?:json)?\s*([\s\S]*?)```/s);
            if (fence && fence[1].trim().startsWith('{')) {
                jsonStr = fence[1].trim();
            } else {
                var s = raw.indexOf('{');
                var e = raw.lastIndexOf('}');
                if (s !== -1 && e > s) jsonStr = raw.substring(s, e + 1);
            }

            var parsed;
            try {
                parsed = JSON.parse(jsonStr);
            } catch (err) {
                showParseErr('JSON inválido: ' + err.message + '. Asegúrate de pegar solo el bloque JSON.');
                return;
            }

            if (typeof parsed !== 'object' || Array.isArray(parsed) || parsed === null) {
                showParseErr('La respuesta debe ser un objeto JSON { }, no un array ni un valor simple.');
                return;
            }

            // Clear inline error and connect to the existing diff + save flow
            if (promptParseError) { promptParseError.classList.add('hidden'); promptParseError.textContent = ''; }

            _suggested = parsed;                        // used by the save button handler
            closePromptModal();
            setStatus('ok', 'Respuesta aplicada — revisa y acepta los campos');
            renderDiffView(parsed, _promptCurrent);     // same flow as the automated AI path
        });
    }
});
