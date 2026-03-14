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

document.addEventListener('DOMContentLoaded', () => {
    AiBatch.init();
});
