/**
 * ai-enhance.js
 * Handles the AI enhancement workflow:
 * 1. POST to enhance URL → receive suggested fields
 * 2. Display diff view with accept/reject checkboxes per field
 * 3. POST accepted fields to save URL
 */

document.addEventListener('DOMContentLoaded', function () {
    var enhanceBtn = document.getElementById('btn-ai-enhance');
    var saveBtn = document.getElementById('btn-ai-save');
    var spinner = document.getElementById('ai-enhance-spinner');
    var btnText = document.getElementById('ai-enhance-text');
    var diffContainer = document.getElementById('ai-diff-container');
    var diffFields = document.getElementById('ai-diff-fields');

    if (!enhanceBtn) return;

    var pageData = document.querySelector('[data-enhance-url]');
    var enhanceUrl = enhanceBtn.dataset.enhanceUrl || (pageData && pageData.dataset.enhanceUrl) || '';
    var saveUrl = (saveBtn && saveBtn.dataset.saveUrl) || (pageData && pageData.dataset.saveUrl) || '';

    enhanceBtn.addEventListener('click', function () {
        if (!enhanceUrl) {
            alert('URL de mejora no configurada.');
            return;
        }

        // Show spinner
        enhanceBtn.disabled = true;
        if (spinner) spinner.classList.remove('hidden');
        if (btnText) btnText.textContent = 'Analizando con IA...';
        if (diffContainer) diffContainer.classList.add('hidden');

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var token = csrfToken ? csrfToken.content : '';

        fetch(enhanceUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        })
        .then(function (response) {
            if (!response.ok) {
                return response.json().then(function (err) {
                    throw new Error(err.message || 'Error en la solicitud');
                });
            }
            return response.json();
        })
        .then(function (data) {
            renderDiffView(data);
        })
        .catch(function (err) {
            alert('Error al conectar con la IA: ' + err.message);
        })
        .finally(function () {
            enhanceBtn.disabled = false;
            if (spinner) spinner.classList.add('hidden');
            if (btnText) btnText.textContent = 'Mejorar con IA';
        });
    });

    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            if (!saveUrl) {
                alert('URL de guardado no configurada.');
                return;
            }

            var accepted = {};
            document.querySelectorAll('.ai-field-accept:checked').forEach(function (cb) {
                var field = cb.dataset.field;
                var valEl = document.querySelector('.ai-suggested-value[data-field="' + field + '"]');
                if (valEl) {
                    accepted[field] = valEl.dataset.value;
                }
            });

            if (Object.keys(accepted).length === 0) {
                alert('No has seleccionado ningún campo para guardar.');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Guardando...';

            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            var token = csrfToken ? csrfToken.content : '';

            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ accepted_fields: accepted }),
            })
            .then(function (response) {
                if (!response.ok) throw new Error('Error al guardar');
                return response.json();
            })
            .then(function (data) {
                saveBtn.textContent = '¡Guardado!';
                saveBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
                saveBtn.classList.add('bg-blue-600');
                // Reload page to reflect changes
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            })
            .catch(function (err) {
                alert('Error al guardar: ' + err.message);
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar Cambios Aceptados';
            });
        });
    }

    function renderDiffView(suggestions) {
        if (!diffFields || !diffContainer) return;
        diffFields.innerHTML = '';

        var fieldLabels = {
            title: 'Título',
            subtitle: 'Subtítulo',
            description: 'Descripción',
            seo_title: 'SEO Title',
            seo_description: 'SEO Description',
            seo_keywords: 'Keywords',
            story: 'Historia / Intro',
            tips_secrets: 'Trucos y Secretos',
        };

        var fields = Object.keys(suggestions);
        if (fields.length === 0) {
            diffFields.innerHTML = '<p class="text-zinc-400 text-sm">La IA no generó sugerencias para esta receta.</p>';
            diffContainer.classList.remove('hidden');
            return;
        }

        fields.forEach(function (field) {
            var suggested = suggestions[field];
            var label = fieldLabels[field] || field;

            var item = document.createElement('div');
            item.className = 'bg-zinc-700/40 rounded-xl border border-zinc-600 p-3 space-y-2';

            item.innerHTML = [
                '<div class="flex items-center justify-between">',
                '<span class="text-xs font-semibold text-zinc-300 uppercase tracking-wide">' + esc(label) + '</span>',
                '<label class="flex items-center gap-2 cursor-pointer">',
                '<input type="checkbox" class="ai-field-accept rounded border-zinc-500 bg-zinc-700 text-emerald-500 focus:ring-emerald-500" data-field="' + esc(field) + '" checked>',
                '<span class="text-xs text-zinc-400">Aceptar</span>',
                '</label>',
                '</div>',
                '<div class="text-xs bg-emerald-950/50 border border-emerald-800/50 text-emerald-300 p-2 rounded-lg leading-relaxed ai-suggested-value" data-field="' + esc(field) + '" data-value="' + esc(String(suggested)) + '">',
                esc(String(suggested)).substring(0, 300) + (String(suggested).length > 300 ? '...' : ''),
                '</div>',
            ].join('');

            diffFields.appendChild(item);
        });

        diffContainer.classList.remove('hidden');
    }

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
});
