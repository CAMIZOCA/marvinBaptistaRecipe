import './bootstrap';
import './modules/mobile-nav';

// Módulos condicionales — solo cargan si el elemento existe en el DOM
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('servings-adjuster')) {
        import('./modules/serving-adjuster.js');
    }
    if (document.querySelector('.ingredient-checkbox')) {
        import('./modules/ingredient-checkboxes.js');
    }
    if (document.querySelector('.step-timer-btn')) {
        import('./modules/step-timer.js');
    }
});
