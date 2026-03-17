/**
 * admin/app.js
 * Entry point for the admin panel JavaScript.
 */
import './recipe-tabs';
import './drag-sort';
import './seo-preview';
import './ai-enhance';
import './import-progress';
import AiBatch from './ai-batch.js';
import { initSeoAnalyzer } from './seo-analyzer.js';

document.addEventListener('DOMContentLoaded', () => {
    AiBatch.init();

    // SEO Analyzer — initialize if the panel exists in the current page
    const panel = document.getElementById('seo-analyzer-panel');
    if (panel) {
        const type = document.body.dataset.analyzerType ?? 'recipe';
        initSeoAnalyzer(type, 'seo-analyzer-panel');
    }
});
