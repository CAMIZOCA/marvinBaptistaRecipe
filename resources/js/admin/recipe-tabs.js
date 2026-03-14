/**
 * recipe-tabs.js
 * Handles the 7-tab recipe editor interface.
 * Remembers active tab in sessionStorage.
 * Also initializes ingredient/step/FAQ row renderers from JSON data.
 */

document.addEventListener('DOMContentLoaded', function () {
    initTabs();
    initIngredients();
    initSteps();
    initFaqs();
    initBookSearch();
});

/* ==================== TABS ==================== */

function initTabs() {
    var tabs = document.querySelectorAll('.recipe-tab');
    var panels = document.querySelectorAll('.tab-panel');
    var storageKey = 'recipe-active-tab';

    if (!tabs.length) return;

    function activateTab(tabId) {
        tabs.forEach(function (t) {
            var isActive = t.dataset.tab === tabId;
            t.classList.toggle('border-amber-500', isActive);
            t.classList.toggle('text-amber-400', isActive);
            t.classList.toggle('border-transparent', !isActive);
            t.classList.toggle('text-zinc-400', !isActive);
            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        panels.forEach(function (p) {
            p.classList.toggle('hidden', p.dataset.tabPanel !== tabId);
        });

        sessionStorage.setItem(storageKey, tabId);
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(this.dataset.tab);
        });
    });

    // Restore or default to first tab
    var savedTab = sessionStorage.getItem(storageKey);
    var firstTabId = tabs[0] ? tabs[0].dataset.tab : null;
    activateTab(savedTab || firstTabId);
}

/* ==================== INGREDIENTS ==================== */

function initIngredients() {
    var container = document.getElementById('ingredients-container');
    var addRowBtn = document.getElementById('add-ingredient-row');
    var addGroupBtn = document.getElementById('add-ingredient-group');
    var jsonTextarea = document.getElementById('ingredients_json');

    if (!container || !jsonTextarea) return;

    var data = [];
    try {
        data = JSON.parse(jsonTextarea.value || '[]');
    } catch (e) {
        data = [];
    }

    // Render existing items
    if (!Array.isArray(data) || data.length === 0) {
        data = [];
        addIngredientRow({});
    } else {
        data.forEach(function (ing) {
            addIngredientRow(ing);
        });
    }

    if (addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            addIngredientRow({});
        });
    }

    if (addGroupBtn) {
        addGroupBtn.addEventListener('click', function () {
            addIngredientRow({ isGroupHeader: true });
        });
    }

    function addIngredientRow(ing) {
        var row = document.createElement('div');
        row.className = 'ingredient-editor-row flex items-center gap-2 p-3 bg-zinc-700/30 rounded-xl border border-zinc-700 mb-2';
        row.draggable = true;
        row.setAttribute('draggable', 'true');

        row.innerHTML = [
            '<div class="cursor-grab text-zinc-500 hover:text-zinc-300 data-sort-handle" title="Arrastrar">',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>',
            '</div>',
            '<input type="text" placeholder="Cantidad" value="' + esc(ing.amount || '') + '" data-field="amount" class="w-20 px-2 py-1.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<input type="text" placeholder="Unidad" value="' + esc(ing.unit || '') + '" data-field="unit" class="w-20 px-2 py-1.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<input type="text" placeholder="Nombre del ingrediente *" value="' + esc(ing.name || '') + '" data-field="name" class="flex-1 px-2 py-1.5 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<input type="text" placeholder="Notas opcionales" value="' + esc(ing.notes || '') + '" data-field="notes" class="w-32 px-2 py-1.5 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<input type="text" placeholder="Grupo" value="' + esc(ing.group || '') + '" data-field="group" class="w-28 px-2 py-1.5 bg-zinc-700 border border-zinc-600 text-zinc-400 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<button type="button" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors shrink-0 delete-ingredient-row" aria-label="Eliminar ingrediente">',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            '</button>',
        ].join('');

        container.appendChild(row);

        // Sync to JSON on any input change
        row.querySelectorAll('input').forEach(function (input) {
            input.addEventListener('input', syncIngredientsJson);
        });

        // Delete row
        row.querySelector('.delete-ingredient-row').addEventListener('click', function () {
            row.remove();
            syncIngredientsJson();
        });

        syncIngredientsJson();
    }

    function syncIngredientsJson() {
        var rows = container.querySelectorAll('.ingredient-editor-row');
        var result = [];
        rows.forEach(function (row, idx) {
            var obj = { order_position: idx };
            row.querySelectorAll('input[data-field]').forEach(function (el) {
                obj[el.dataset.field] = el.value;
            });
            result.push(obj);
        });
        jsonTextarea.value = JSON.stringify(result);
    }
}

/* ==================== STEPS ==================== */

function initSteps() {
    var container = document.getElementById('steps-container');
    var addBtn = document.getElementById('add-step');
    var jsonTextarea = document.getElementById('steps_json');
    var emptyMsg = document.getElementById('steps-empty');

    if (!container || !jsonTextarea) return;

    var data = [];
    try {
        data = JSON.parse(jsonTextarea.value || '[]');
    } catch (e) {
        data = [];
    }

    data.forEach(function (step) {
        addStepRow(step);
    });

    updateEmpty();

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            addStepRow({});
        });
    }

    function addStepRow(step) {
        var index = container.querySelectorAll('.step-editor-row').length + 1;

        var row = document.createElement('div');
        row.className = 'step-editor-row bg-zinc-700/30 rounded-xl border border-zinc-700 p-4 space-y-3';
        row.draggable = true;

        row.innerHTML = [
            '<div class="flex items-center gap-3">',
            '<div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center text-white font-bold text-sm step-number shrink-0">' + index + '</div>',
            '<input type="text" placeholder="Título del paso (opcional)" value="' + esc(step.title || '') + '" data-field="title" class="flex-1 px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<input type="number" placeholder="Duración (min)" value="' + esc(String(step.duration_minutes || '')) + '" data-field="duration_minutes" min="0" class="w-32 px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<button type="button" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors delete-step-row">',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            '</button>',
            '</div>',
            '<textarea placeholder="Descripción del paso..." data-field="description" rows="3" class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 resize-none">' + esc(step.description || '') + '</textarea>',
        ].join('');

        container.appendChild(row);

        row.querySelectorAll('input, textarea').forEach(function (el) {
            el.addEventListener('input', syncStepsJson);
        });

        row.querySelector('.delete-step-row').addEventListener('click', function () {
            row.remove();
            renumberSteps();
            syncStepsJson();
            updateEmpty();
        });

        syncStepsJson();
        updateEmpty();
    }

    function renumberSteps() {
        container.querySelectorAll('.step-number').forEach(function (el, idx) {
            el.textContent = idx + 1;
        });
    }

    function syncStepsJson() {
        var rows = container.querySelectorAll('.step-editor-row');
        var result = [];
        rows.forEach(function (row, idx) {
            var obj = { order_position: idx };
            row.querySelectorAll('[data-field]').forEach(function (el) {
                obj[el.dataset.field] = el.value;
            });
            result.push(obj);
        });
        jsonTextarea.value = JSON.stringify(result);
    }

    function updateEmpty() {
        if (!emptyMsg) return;
        var hasRows = container.querySelectorAll('.step-editor-row').length > 0;
        emptyMsg.classList.toggle('hidden', hasRows);
    }
}

/* ==================== FAQ ==================== */

function initFaqs() {
    var container = document.getElementById('faq-container');
    var addBtn = document.getElementById('add-faq');
    var jsonTextarea = document.getElementById('faqs_json');
    var emptyMsg = document.getElementById('faq-empty');

    if (!container || !jsonTextarea) return;

    var data = [];
    try {
        data = JSON.parse(jsonTextarea.value || '[]');
    } catch (e) {
        data = [];
    }

    data.forEach(function (faq) {
        addFaqRow(faq);
    });

    updateEmpty();

    if (addBtn) {
        addBtn.addEventListener('click', function () {
            addFaqRow({});
        });
    }

    function addFaqRow(faq) {
        var row = document.createElement('div');
        row.className = 'faq-editor-row bg-zinc-700/30 rounded-xl border border-zinc-700 p-4 space-y-3';

        row.innerHTML = [
            '<div class="flex items-start gap-3">',
            '<div class="flex-1 space-y-3">',
            '<input type="text" placeholder="Pregunta" value="' + esc(faq.question || '') + '" data-field="question" class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">',
            '<textarea placeholder="Respuesta..." data-field="answer" rows="3" class="w-full px-3 py-2 bg-zinc-700 border border-zinc-600 text-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-amber-500 resize-none">' + esc(faq.answer || '') + '</textarea>',
            '</div>',
            '<button type="button" class="p-1.5 text-zinc-500 hover:text-red-400 transition-colors shrink-0 delete-faq-row mt-1">',
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
            '</button>',
            '</div>',
        ].join('');

        container.appendChild(row);

        row.querySelectorAll('input, textarea').forEach(function (el) {
            el.addEventListener('input', syncFaqsJson);
        });

        row.querySelector('.delete-faq-row').addEventListener('click', function () {
            row.remove();
            syncFaqsJson();
            updateEmpty();
        });

        syncFaqsJson();
        updateEmpty();
    }

    function syncFaqsJson() {
        var rows = container.querySelectorAll('.faq-editor-row');
        var result = [];
        rows.forEach(function (row) {
            var obj = {};
            row.querySelectorAll('[data-field]').forEach(function (el) {
                obj[el.dataset.field] = el.value;
            });
            result.push(obj);
        });
        jsonTextarea.value = JSON.stringify(result);
    }

    function updateEmpty() {
        if (!emptyMsg) return;
        emptyMsg.classList.toggle('hidden', container.querySelectorAll('.faq-editor-row').length > 0);
    }
}

/* ==================== BOOK SEARCH ==================== */

function initBookSearch() {
    var searchInput = document.getElementById('book-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        var query = this.value.toLowerCase().trim();
        document.querySelectorAll('.book-item').forEach(function (item) {
            var title = (item.dataset.bookTitle || '').toLowerCase();
            item.style.display = (!query || title.includes(query)) ? '' : 'none';
        });
    });
}

function esc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}
