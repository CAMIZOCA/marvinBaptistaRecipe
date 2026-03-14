/**
 * drag-sort.js
 * HTML5 Drag API sortable lists that update a hidden JSON textarea.
 * Containers must have [data-sortable] and [data-json-target="<textarea-id>"].
 * Items must have draggable="true" and a child with class "data-sort-handle".
 */

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-sortable]').forEach(function (container) {
        initSortable(container);
    });
});

function initSortable(container) {
    var jsonTargetId = container.dataset.jsonTarget;
    var jsonTextarea = jsonTargetId ? document.getElementById(jsonTargetId) : null;
    var dragging = null;

    container.addEventListener('dragstart', function (e) {
        dragging = e.target.closest('[draggable="true"]');
        if (!dragging) return;
        dragging.classList.add('opacity-50', 'scale-95');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', '');
    });

    container.addEventListener('dragend', function (e) {
        if (!dragging) return;
        dragging.classList.remove('opacity-50', 'scale-95');
        // Clear highlights
        container.querySelectorAll('[draggable="true"]').forEach(function (item) {
            item.classList.remove('border-amber-400', 'border-t-2');
        });
        dragging = null;
        serializeToJson(container, jsonTextarea);
    });

    container.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        var target = e.target.closest('[draggable="true"]');
        if (!target || target === dragging) return;

        // Determine insert position
        var rect = target.getBoundingClientRect();
        var midpoint = rect.top + rect.height / 2;
        if (e.clientY < midpoint) {
            container.insertBefore(dragging, target);
        } else {
            container.insertBefore(dragging, target.nextSibling);
        }
    });

    container.addEventListener('drop', function (e) {
        e.preventDefault();
    });
}

function serializeToJson(container, textarea) {
    if (!textarea) return;

    var items = container.querySelectorAll('[draggable="true"]');
    var data = [];
    items.forEach(function (item, index) {
        var obj = {};
        // Gather all input/select/textarea values within the item
        item.querySelectorAll('input[data-field], select[data-field], textarea[data-field]').forEach(function (el) {
            obj[el.dataset.field] = el.value;
        });
        obj.order_position = index;
        data.push(obj);
    });

    textarea.value = JSON.stringify(data);

    // Dispatch change event so other listeners can react
    var event = new Event('change', { bubbles: true });
    textarea.dispatchEvent(event);
}

// Export for use in other modules
window.DragSort = { serializeToJson: serializeToJson };
