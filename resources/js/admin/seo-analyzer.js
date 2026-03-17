/**
 * SEO & Content Analyzer — Yoast-style traffic light for recipe and page editors.
 * Evaluates content quality in real time and shows a score with actionable checks.
 *
 * Usage:
 *   import { initSeoAnalyzer } from './seo-analyzer.js';
 *   initSeoAnalyzer('recipe', 'seo-analyzer-panel');
 *   initSeoAnalyzer('page',   'seo-analyzer-panel');
 */

// ── Helpers ────────────────────────────────────────────────────────────────────

function val(selector) {
    const el = document.querySelector(selector);
    return el ? (el.value || '').trim() : '';
}

function wordCount(html) {
    if (!html) return 0;
    const text = html.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
    return text ? text.split(' ').filter(w => w.length > 1).length : 0;
}

function parseJsonField(selector) {
    try {
        const raw = val(selector);
        if (!raw) return [];
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

function debounce(fn, ms) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
}

// ── Check definitions ──────────────────────────────────────────────────────────

const RECIPE_CHECKS = [
    {
        id: 'seo_title_filled',
        label: 'SEO title rellenado',
        hint: 'El SEO title aparece en Google. Ve a la pestaña SEO y rellena este campo.',
        points: 12,
        test: () => val('#seo_title').length > 0,
    },
    {
        id: 'seo_title_length',
        label: 'SEO title entre 40–60 caracteres',
        hint: 'Un título demasiado corto o largo pierde visibilidad. Apunta a 50–55 caracteres.',
        points: 8,
        test: () => { const l = val('#seo_title').length; return l >= 40 && l <= 60; },
    },
    {
        id: 'seo_desc_filled',
        label: 'SEO description rellenada',
        hint: 'La meta description mejora el CTR en buscadores. Rellénala en la pestaña SEO.',
        points: 12,
        test: () => val('#seo_description').length > 0,
    },
    {
        id: 'seo_desc_length',
        label: 'SEO description entre 120–160 caracteres',
        hint: 'Google muestra ~155 caracteres. Usa el espacio al máximo sin pasarte.',
        points: 8,
        test: () => { const l = val('#seo_description').length; return l >= 120 && l <= 160; },
    },
    {
        id: 'seo_keywords',
        label: 'Al menos 1 palabra clave SEO definida',
        hint: 'Define la keyword principal de la receta (ej: "caldo de pollo ecuatoriano").',
        points: 5,
        test: () => val('[name="seo_keywords"]').length > 0,
    },
    {
        id: 'featured_image',
        label: 'Imagen destacada configurada',
        hint: 'Sin imagen no hay miniatura en redes sociales. Sube una en la pestaña Contenido.',
        points: 10,
        test: () => val('[name="featured_image"]').length > 0,
    },
    {
        id: 'image_alt',
        label: 'Alt text de imagen configurado',
        hint: 'El alt text ayuda al SEO de imágenes y a la accesibilidad. Descríbela brevemente.',
        points: 8,
        test: () => val('[name="image_alt"]').length > 0,
    },
    {
        id: 'description',
        label: 'Descripción de la receta tiene contenido',
        hint: 'Añade una introducción apetitosa en la pestaña Contenido (editor de texto).',
        points: 7,
        test: () => wordCount(val('#description')) > 0,
    },
    {
        id: 'ingredients',
        label: 'Al menos 3 ingredientes definidos',
        hint: 'Añade los ingredientes en la pestaña Ingredientes para el Schema.org completo.',
        points: 10,
        test: () => parseJsonField('#ingredients_json').length >= 3,
    },
    {
        id: 'steps',
        label: 'Al menos 3 pasos de preparación',
        hint: 'Los pasos estructurados mejoran los rich snippets de Google. Ve a Preparación.',
        points: 10,
        test: () => parseJsonField('#steps_json').length >= 3,
    },
    {
        id: 'faqs',
        label: 'Al menos 1 pregunta frecuente (FAQ)',
        hint: 'Las FAQs generan FAQ Schema y pueden aparecer directamente en Google. Ve a FAQ.',
        points: 5,
        test: () => parseJsonField('#faqs_json').length >= 1,
    },
    {
        id: 'story',
        label: 'Historia / introducción rellenada',
        hint: 'Una introducción con contexto cultural aumenta el tiempo de permanencia en la página.',
        points: 5,
        test: () => wordCount(val('#story')) > 10,
    },
];

const PAGE_CHECKS = [
    {
        id: 'title',
        label: 'Título de la página rellenado',
        hint: 'El título es imprescindible para la navegación y el SEO básico.',
        points: 10,
        test: () => val('[name="title"]').length > 0,
    },
    {
        id: 'seo_title_filled',
        label: 'SEO title rellenado',
        hint: 'Es el título que aparece en las pestañas del navegador y en Google.',
        points: 15,
        test: () => val('[name="seo_title"]').length > 0,
    },
    {
        id: 'seo_title_length',
        label: 'SEO title entre 40–60 caracteres',
        hint: 'Apunta a 50–55 caracteres para que no se corte en los resultados de búsqueda.',
        points: 10,
        test: () => { const l = val('[name="seo_title"]').length; return l >= 40 && l <= 60; },
    },
    {
        id: 'seo_desc_filled',
        label: 'SEO description rellenada',
        hint: 'La meta description es lo primero que el usuario ve en Google.',
        points: 15,
        test: () => val('[name="seo_description"]').length > 0,
    },
    {
        id: 'seo_desc_length',
        label: 'SEO description entre 120–160 caracteres',
        hint: 'Usa los 155 caracteres disponibles para una llamada a la acción persuasiva.',
        points: 10,
        test: () => { const l = val('[name="seo_description"]').length; return l >= 120 && l <= 160; },
    },
    {
        id: 'content_present',
        label: 'La página tiene contenido',
        hint: 'Una página sin texto es invisible para Google. Añade el contenido en el editor.',
        points: 20,
        test: () => wordCount(val('#page_content')) > 0,
    },
    {
        id: 'content_words',
        label: 'Contenido con 300 o más palabras',
        hint: 'Google valora el contenido extenso. Apunta a 300+ palabras para posicionar mejor.',
        points: 20,
        test: () => wordCount(val('#page_content')) >= 300,
    },
];

// ── Scoring ────────────────────────────────────────────────────────────────────

function scoreColor(score) {
    if (score >= 75) return 'green';
    if (score >= 50) return 'yellow';
    return 'red';
}

const COLOR_CLASSES = {
    green:  { light: 'bg-emerald-500', badge: 'bg-emerald-900/60 text-emerald-300',  label: 'Bueno' },
    yellow: { light: 'bg-amber-400',   badge: 'bg-amber-900/60 text-amber-300',      label: 'Mejorable' },
    red:    { light: 'bg-red-500',     badge: 'bg-red-900/60 text-red-300',          label: 'Incompleto' },
};

// ── Icons ──────────────────────────────────────────────────────────────────────

const ICON_PASS = `<svg class="w-3.5 h-3.5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
</svg>`;

const ICON_FAIL = `<svg class="w-3.5 h-3.5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
</svg>`;

const ICON_CHEVRON_DOWN = `<svg id="seo-chevron" class="w-4 h-4 text-zinc-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
</svg>`;

// ── Renderer ───────────────────────────────────────────────────────────────────

function render(panelEl, results, score) {
    const color  = scoreColor(score);
    const cls    = COLOR_CLASSES[color];

    // Traffic light dot
    const dot = panelEl.querySelector('#seo-traffic-light');
    if (dot) {
        dot.className = `w-3.5 h-3.5 rounded-full shrink-0 transition-colors duration-300 ${cls.light}`;
    }

    // Score badge
    const badge = panelEl.querySelector('#seo-score-badge');
    if (badge) {
        badge.textContent = `${score}/100`;
        badge.className   = `text-xs font-bold px-2 py-0.5 rounded-full transition-colors duration-300 ${cls.badge}`;
    }

    // Status label
    const statusEl = panelEl.querySelector('#seo-status-label');
    if (statusEl) statusEl.textContent = cls.label;

    // Checks list
    const list = panelEl.querySelector('#seo-checks-list');
    if (!list) return;

    // Separate: passed first only if > 60 passed, otherwise failed first
    const failed = results.filter(r => !r.passed);
    const passed = results.filter(r => r.passed);
    const ordered = [...failed, ...passed];

    list.innerHTML = ordered.map(r => `
        <div class="flex items-start gap-2 py-1">
            ${r.passed ? ICON_PASS : ICON_FAIL}
            <div class="min-w-0">
                <p class="text-xs ${r.passed ? 'text-zinc-400' : 'text-zinc-200'} leading-snug">
                    ${r.label}
                    <span class="text-zinc-600 ml-1">(+${r.points})</span>
                </p>
                ${!r.passed && r.hint ? `<p class="text-xs text-zinc-500 mt-0.5 leading-snug">${r.hint}</p>` : ''}
            </div>
        </div>
    `).join('');
}

// ── Core analyze ───────────────────────────────────────────────────────────────

function analyze(checks, panelEl) {
    let score = 0;
    const results = checks.map(check => {
        let passed = false;
        try { passed = check.test(); } catch { passed = false; }
        if (passed) score += check.points;
        return { ...check, passed };
    });
    render(panelEl, results, score);
}

// ── Panel toggle ───────────────────────────────────────────────────────────────

function setupToggle(panelEl) {
    const header = panelEl.querySelector('#seo-panel-header');
    const body   = panelEl.querySelector('#seo-checks-list');
    const chevron = panelEl.querySelector('#seo-chevron');
    if (!header || !body) return;

    // Start expanded
    let expanded = true;

    header.addEventListener('click', () => {
        expanded = !expanded;
        body.style.display = expanded ? '' : 'none';
        if (chevron) {
            chevron.style.transform = expanded ? '' : 'rotate(-90deg)';
        }
    });
}

// ── Listener setup ─────────────────────────────────────────────────────────────

function attachListeners(type, checks, panelEl) {
    const run = debounce(() => analyze(checks, panelEl), 300);

    if (type === 'recipe') {
        // Standard inputs
        ['#seo_title', '#seo_description', '[name="seo_keywords"]',
         '[name="featured_image"]', '[name="image_alt"]',
         '[name="title"]'].forEach(sel => {
            document.querySelector(sel)?.addEventListener('input', run);
        });

        // Trix editors (fire trix-change on the editor element, syncs to hidden input)
        document.querySelectorAll('trix-editor').forEach(editor => {
            editor.addEventListener('trix-change', run);
        });

        // JSON hidden textareas (recipe-tabs.js updates them programmatically)
        // Use MutationObserver since they don't fire 'input' when set via .value
        ['#ingredients_json', '#steps_json', '#faqs_json'].forEach(sel => {
            const el = document.querySelector(sel);
            if (!el) return;
            // Also listen to input events (some paths do trigger them)
            el.addEventListener('input', run);
            // MutationObserver for attribute/value changes
            const obs = new MutationObserver(run);
            obs.observe(el, { attributes: true, childList: true, subtree: true });
        });

        // Observe JSON textarea value changes via polling fallback (recipe-tabs.js uses .value =)
        let prevIngr = '', prevSteps = '', prevFaqs = '';
        setInterval(() => {
            const ingr  = val('#ingredients_json');
            const steps = val('#steps_json');
            const faqs  = val('#faqs_json');
            if (ingr !== prevIngr || steps !== prevSteps || faqs !== prevFaqs) {
                prevIngr = ingr; prevSteps = steps; prevFaqs = faqs;
                run();
            }
        }, 1500);

    } else if (type === 'page') {
        ['[name="title"]', '[name="seo_title"]', '[name="seo_description"]'].forEach(sel => {
            document.querySelector(sel)?.addEventListener('input', run);
        });
        document.querySelectorAll('trix-editor').forEach(editor => {
            editor.addEventListener('trix-change', run);
        });
    }
}

// ── Public API ─────────────────────────────────────────────────────────────────

export function initSeoAnalyzer(type, panelId) {
    const panelEl = document.getElementById(panelId);
    if (!panelEl) return;

    const checks = type === 'page' ? PAGE_CHECKS : RECIPE_CHECKS;

    // Setup collapsible header
    setupToggle(panelEl);

    // Initial analysis
    analyze(checks, panelEl);

    // Attach real-time listeners
    attachListeners(type, checks, panelEl);
}
