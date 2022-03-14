import '../css/app.css'

import Alpine from 'alpinejs'
import Chart from 'chart.js/auto'
import FormsAlpinePlugin from '../../../forms/dist/module.esm'
import Focus from '@alpinejs/focus'
import Persist from '@alpinejs/persist'
import Collapse from '@alpinejs/collapse'
import Tooltip from "@ryangjchandler/alpine-tooltip"

Alpine.plugin(FormsAlpinePlugin)
Alpine.plugin(Focus)
Alpine.plugin(Persist)
Alpine.plugin(Collapse)
Alpine.plugin(Tooltip)

Alpine.store('sidebar', {
    isOpen: false,

    collapsedGroups: Alpine.$persist([]).as('collapsedGroups'),

    groupIsCollapsed(group) {
        return this.collapsedGroups.includes(group)
    },

    toggleCollapsedGroup(group) {
        this.collapsedGroups = this.collapsedGroups.includes(group) ?
            this.collapsedGroups.filter(g => g !== group) :
            this.collapsedGroups.concat(group)
    },

    close() {
        this.isOpen = false
    },

    open() {
        this.isOpen = true
    },
})

Chart.defaults.font.family = `'DM Sans', sans-serif`
Chart.defaults.color = '#6b7280'

window.Alpine = Alpine
window.Chart = Chart

Alpine.start()
