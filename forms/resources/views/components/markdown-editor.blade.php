@pushonce('filament-styles:markdown-editor-component')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/mdhl.css">

    <style>
        [x-data^="markdownEditor"] textarea {
            color: transparent;
            caret-color: black;
        }
    </style>
@endpushonce

@pushonce('filament-scripts:markdown-editor-component')
    <script type="module" src="https://cdn.jsdelivr.net/npm/@github/markdown-toolbar-element@1.4.0/dist/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mdhl@0.0.6/dist/mdhl.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        function markdownEditor(config) {
            return {
                overlay: null,

                preview: '',

                value: '',

                tab: 'write',

                init: function () {
                    this.value = this.$refs.textarea.value

                    this.$refs.overlay.style.padding = window.getComputedStyle(this.$refs.textarea).padding

                    this.overlay = mdhl.highlight(this.$refs.textarea.value)

                    this.$watch('tab', () => {
                        if (this.tab !== 'preview') return

                        this.preview = marked(this.value)
                    })
                },
                resize: function () {
                    this.$el.style.height = this.$refs.textarea.style.height
                    this.overlay = mdhl.highlight(this.value = this.$refs.textarea.value)
                }
            }
        }
    </script>
@endpushonce

<x-forms::field-group
    :column-span="$formComponent->getColumnSpan()"
    :error-key="$formComponent->getName()"
    :for="$formComponent->getId()"
    :help-message="$formComponent->getHelpMessage()"
    :hint="$formComponent->getHint()"
    :label="$formComponent->getLabel()"
    :required="$formComponent->isRequired()"
>
    <div
        x-data="markdownEditor({
        })"
        x-init="init"
        wire:ignore
    >
        <div class="space-y-2">
            <div class="flex items-stretch justify-between">
                <div class="flex items-center space-x-1">
                    <x-filament::button
                        x-on:click.prevent="tab = 'write'"
                        x-bind:class="{ 'text-gray-500 bg-gray-100': tab !== 'write' }"
                    >
                        Write
                    </x-filament::button>
                    <x-filament::button
                        x-on:click.prevent="tab = 'preview'"
                        x-bind:class="{ 'text-gray-500 bg-gray-100': tab !== 'preview' }"
                    >
                        Preview
                    </x-filament::button>
                </div>
                <markdown-toolbar for="{{ $formComponent->getId() }}" class="flex items-stretch space-x-4">
                    <div class="flex items-stretch space-x-1">
                        <x-filament::button size="small" class="text-base">
                            <md-bold>𝐁</md-bold>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-italic>𝑰</md-italic>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-strikethrough>
                                <strike>𝐒</strike>
                            </md-strikethrough>
                        </x-filament::button>
                    </div>
                    <div class="flex items-stretch space-x-1">
                        <x-filament::button size="small" class="text-base">
                            <md-link class="w-full h-full">
                                <x-heroicon-o-link class="w-4" />
                            </md-link>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-image class="w-full h-full">
                                <x-heroicon-o-photograph class="w-4" />
                            </md-image>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-code class="w-full h-full">
                                <x-heroicon-o-code class="w-4" />
                            </md-code>
                        </x-filament::button>
                    </div>
                    <div class="flex items-stretch space-x-1">
                        <x-filament::button size="small" class="text-base">
                            <md-unordered-list class="w-full h-full">
                                <svg version="1" xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M4 4a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm4 3h14v-2H8v2zm0-6h14v-2H8v2zm0-8v2h14V5H8z"/></svg>
                            </md-unordered-list>
                        </x-filament::button>
                        <x-filament::button size="small" class="text-base">
                            <md-ordered-list class="w-full h-full">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M2 17h2v.5H3v1h1v.5H2v1h3v-4H2v1zm1-9h1V4H2v1h1v3zm-1 3h1.8L2 13.1v.9h3v-1H3.2L5 10.9V10H2v1zm5-6v2h14V5H7zm0 14h14v-2H7v2zm0-6h14v-2H7v2z"/></svg>
                            </md-ordered-list>
                        </x-filament::button>
                    </div>
                </markdown-toolbar>
            </div>

            <div x-show="tab === 'write'" class="relative w-full h-full" style="min-height: 150px;">
                <textarea
                    {!! $formComponent->isAutofocused() ? 'autofocus' : null !!}
                    {!! $formComponent->isDisabled() ? 'disabled' : null !!}
                    {!! $formComponent->getId() ? "id=\"{$formComponent->getId()}\"" : null !!}
                    {!! $formComponent->getName() ? "{$formComponent->getBindingAttribute()}=\"{$formComponent->getName()}\"" : null !!}
                    {!! $formComponent->getPlaceholder() ? "placeholder=\"{$formComponent->getPlaceholder()}\"" : null !!}
                    {!! $formComponent->isRequired() ? 'required' : null !!}
                    @input="resize"
                    class="absolute bg-transparent top-0 left-0 block z-1 w-full h-full min-h-full rounded resize-none shadow-sm placeholder-gray-400 focus:placeholder-gray-500 placeholder-opacity-100 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $errors->has($formComponent->getName()) ? 'border-danger-600 motion-safe:animate-shake' : 'border-gray-300' }}"
                    x-ref="textarea"
                ></textarea>
                <div class="w-full h-full text-black" x-ref="overlay" x-html="overlay"></div>
            </div>
            <div class="block w-full h-full min-h-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:border-blue-300" x-show="tab === 'preview'" style="min-height: 150px;">
                <div class="prose" x-html="preview"></div>
            </div>
        </div>
    </div>
</x-forms::field-group>
