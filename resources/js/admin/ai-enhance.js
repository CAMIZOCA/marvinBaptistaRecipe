/**
 * ai-enhance.js — AI Enhancement workflow
 * 1. POST to enhance URL → receive { success, current, suggested }
 * 2. Display each suggested field with accept checkbox + current vs new diff
 * 3. POST accepted fields to save URL as { fields: {...} }
 */

document.addEventListener('DOMContentLoaded', function () {
    var enhanceBtn    = document.getElementById('btn-ai-enhance');
    var saveBtn       = document.getElementById('btn-ai-save');
    var spinner       = document.getElementById('ai-enhance-spinner');
    var btnText       = document.getElementById('ai-enhance-text');
    var diffContainer = document.getElementById('ai-diff-container');
    var diffFields    = document.getElementById('ai-diff-fields');

    if (!enhanceBtn) return;

    var pageData   = document.querySelector('[data-enhance-url]');
    var enhanceUrl = enhanceBtn.dataset.enhanceUrl || (pageData && pageData.dataset.enhanceUrl) || '';
    var saveUrl    = (saveBtn && saveBtn.dataset.saveUrl) || (pageData && pageData.dataset.saveUrl) || '';

    // Store full suggested object for save step
    var _suggested = {};

    /* ─── Enhance button ──────────────────────────────────────── */
    enhanceBtn.addEventListener('click', function () {
        if (!enhanceUrl) { alert('URL de mejora no configurada.'); return; }

        enhanceBtn.disabled = true;
        if (spinner)       spinner.classList.remove('hidden');
        if (btnText)       btnText.textContent = 'Analizando con IA...';
        if (diffContainer) diffContainer.classList.add('hidden');

        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        fetch(enhanceUrl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body:    JSON.stringify({}),
        })
        .then(function (res) {
            if (!res.ok) return res.json().then(function (e) { throw new Error(e.error || e.message || 'Error'); });
            return res.json();
        })
        .then(function (data) {
            _suggested = data.suggested || {};
            renderDiffView(data.suggested || {}, data.current || {});
        })
        .catch(function (err) { alert('Error al conectar con la IA: ' + err.message); })
        .finally(function () {
            enhanceBtn.disabled = false;
            if (spinner) spinner.classList.add('hidden');
            if (btnText) btnText.textContent = 'Mejorar con IA';
        });
    });

    /* ─── Save button ─────────────────────────────────────────── */
    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            if (!saveUrl) { alert('URL de guardado no configurada.'); return; }

            var fields = {};
            document.querySelectorAll('.ai-field-accept:checked').forEach(function (cb) {
                var f = cb.dataset.field;
                if (_suggested[f] !== undefined) fields[f] = _suggested[f];
            });

            if (Object.keys(fields).length === 0) {
                alert('No has seleccionado ningún campo para guardar.');
                return;
            }

            saveBtn.disabled    = true;
            saveBtn.textContent = 'Guardando...';

            var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

            fetch(saveUrl, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body:    JSON.stringify({ fields: fields }),
            })
            .then(function (res) {
                if (!res.ok) throw new Error('Error al guardar');
                return res.json();
            })
            .then(function () {
                saveBtn.textContent = '¡Guardado!';
                saveBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
                saveBtn.classList.add('bg-blue-600');
                setTimeout(function () { window.location.reload(); }, 900);
            })
            .catch(function (err) {
                alert('Error al guardar: ' + err.message);
                saveBtn.disabled    = false;
                saveBtn.textContent = 'Guardar Cambios Aceptados';
            });
        });
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
                // tips_secrets can be string or array
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
                (Array.isArray(val) ? val : [val]).forEach(function (item) {
                    listHtml += '<li class="leading-relaxed">' + esc(String(item)) + '</li>';
                });
                listHtml += '</ul>';
                sugBlock.innerHTML += listHtml;
            }

            body.appendChild(sugBlock);
            item.appendChild(body);
            diffFields.appendChild(item);
        });

        diffContainer.classList.remove('hidden');

        // Scroll into view
        diffContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
});
