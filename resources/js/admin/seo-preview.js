/**
 * seo-preview.js
 * Live updates the Google search snippet preview and character counters
 * as the user types in SEO title and description inputs.
 */

document.addEventListener('DOMContentLoaded', function () {
    var titleInput = document.getElementById('seo_title');
    var descInput = document.getElementById('seo_description');
    var titleCount = document.getElementById('seo-title-count');
    var descCount = document.getElementById('seo-desc-count');
    var previewTitle = document.getElementById('seo-preview-title');
    var previewDesc = document.getElementById('seo-preview-description');

    if (!titleInput && !descInput) return;

    function updateTitle() {
        var val = titleInput ? titleInput.value : '';
        var len = val.length;

        // Update counter
        if (titleCount) {
            titleCount.textContent = len + '/60';
            titleCount.className = 'text-xs font-mono ' + (
                len > 60 ? 'text-red-400' :
                len > 50 ? 'text-yellow-400' :
                'text-emerald-400'
            );
        }

        // Update preview
        if (previewTitle) {
            previewTitle.textContent = val || 'Título de la receta';
        }
    }

    function updateDesc() {
        var val = descInput ? descInput.value : '';
        var len = val.length;

        // Update counter
        if (descCount) {
            descCount.textContent = len + '/160';
            descCount.className = 'text-xs font-mono ' + (
                len > 160 ? 'text-red-400' :
                len > 140 ? 'text-yellow-400' :
                'text-emerald-400'
            );
        }

        // Update preview
        if (previewDesc) {
            previewDesc.textContent = val || 'Meta descripción de la receta...';
        }
    }

    if (titleInput) {
        titleInput.addEventListener('input', updateTitle);
        updateTitle(); // Initialize on load
    }

    if (descInput) {
        descInput.addEventListener('input', updateDesc);
        updateDesc();
    }
});
