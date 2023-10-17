import Sortable from 'sortablejs'

window.Sortable = Sortable

export default (Alpine) => {
    Alpine.directive('sortable', (el) => {
        el.sortable = Sortable.create(el, {
            draggable: '[x-sortable-item]',
            handle: '[x-sortable-handle]',
            dataIdAttr: 'x-sortable-item',
            animation: parseInt(el.dataset?.sortableAnimation) || 300,
            // Class name for the drop placeholder
            // Applying styles on packages/forms/resources/css/components/sortable.css
            ghostClass: "sortable-ghost",
        })
    })
}
