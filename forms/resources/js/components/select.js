import '../../css/components/select.css'

export default (Alpine) => {
    Alpine.data('selectFormComponent', ({
        getOptionLabelUsing,
        getOptionLabelsUsing,
        getOptionsUsing,
        getSearchResultsUsing,
        isAutofocused,
        isMultiple,
        hasDynamicOptions,
        hasDynamicSearchResults,
        loadingMessage,
        maxItems,
        noSearchResultsMessage,
        options,
        placeholder,
        searchingMessage,
        searchPrompt,
        state,
    }) => {
        return {
            isSearching: false,

            select: null,

            selectedOptions: [],

            isStateBeingUpdated: false,

            state,

            init: async function () {
                this.select = new Choices(this.$refs.input, {
                    allowHTML: false,
                    duplicateItemsAllowed: false,
                    itemSelectText: '',
                    loadingText: loadingMessage,
                    maxItemCount: maxItems ?? -1,
                    noChoicesText: searchPrompt,
                    noResultsText: noSearchResultsMessage,
                    placeholderValue: placeholder,
                    removeItemButton: true,
                    renderChoiceLimit: 50,
                    searchChoices: ! hasDynamicSearchResults,
                    searchFields: ['label'],
                    searchResultLimit: 50,
                })

                await this.refreshChoices({ withInitialOptions: true })

                if (! [null, undefined, ''].includes(this.state)) {
                    this.select.setChoiceByValue(this.formatState(this.state))
                }

                if (isAutofocused) {
                    this.select.showDropdown()
                }

                this.$refs.input.addEventListener('change', () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.isStateBeingUpdated = true
                    this.state = this.select.getValue(true)
                    this.$nextTick(() => this.isStateBeingUpdated = false)
                })

                if (hasDynamicOptions) {
                    this.$refs.input.addEventListener('showDropdown', async () => {
                        this.select.clearChoices()
                        await this.select.setChoices(
                            [{ value: '', label: loadingMessage, disabled: true }],
                        )

                        await this.refreshChoices()
                    })
                }

                if (hasDynamicSearchResults) {
                    this.$refs.input.addEventListener('search', async (event) => {
                        let search = event.detail.value?.trim()

                        if ([null, undefined, ''].includes(search)) {
                            return
                        }

                        this.isSearching = true

                        this.select.clearChoices()
                        await this.select.setChoices(
                            [{ value: '', label: searchingMessage, disabled: true }],
                        )
                    })

                    this.$refs.input.addEventListener('search', Alpine.debounce(async (event) => {
                        await this.refreshChoices({
                            search: event.detail.value?.trim(),
                        })

                        this.isSearching = false
                    }, 1000))
                }

                this.$watch('state', async () => {
                    if (this.isStateBeingUpdated) {
                        return
                    }

                    this.select.clearStore()

                    await this.refreshChoices({
                        withInitialOptions: ! hasDynamicOptions,
                    })

                    if (! [null, undefined, ''].includes(this.state)) {
                        this.select.setChoiceByValue(this.formatState(this.state))
                    }
                })
            },

            refreshChoices: async function (config = {}) {
                const choices = await this.getChoices(config)

                await this.select.setChoices(
                    choices,
                    'value',
                    'label',
                    true,
                )
            },

            getChoices: async function (config = {}) {
                const options = await this.getOptions(config)

                return this.transformOptionsIntoChoices({
                    ...options,
                    ...await this.getMissingOptions(options),
                })
            },

            getOptions: async function ({ search, withInitialOptions }) {
                if (withInitialOptions) {
                    return options
                }

                if ((search !== '') && (search !== null) && (search !== undefined)) {
                    return await getSearchResultsUsing(search)
                }

                return await getOptionsUsing()
            },

            transformOptionsIntoChoices: function (options) {
                return Object.entries(options)
                    .map(([value, label]) => ({
                        label,
                        value,
                    }))
            },

            formatState: function (state) {
                if (isMultiple) {
                    return (state ?? []).map((item) => item?.toString())
                }

                return state?.toString()
            },

            getMissingOptions: async function (options) {
                if ([null, undefined, '', [], {}].includes(this.state)) {
                    return {}
                }

                if (! options.length) {
                    options = {}
                }

                if (isMultiple) {
                    if (this.state.every((value) => value in options)) {
                        return {}
                    }

                    return await getOptionLabelsUsing()
                }

                if (this.state in options) {
                    return options
                }

                let missingOptions = {}
                missingOptions[this.state] = await getOptionLabelUsing()
                return missingOptions
            },
        }
    })
}
