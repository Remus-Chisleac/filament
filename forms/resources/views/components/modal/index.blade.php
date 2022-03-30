@props([
    'actions' => null,
    'ariaLabelledby' => null,
    'closeEventName' => 'close-modal',
    'displayClasses' => 'inline-block',
    'footer' => null,
    'header' => null,
    'heading' => null,
    'id' => null,
    'openEventName' => 'open-modal',
    'subheading' => null,
    'trigger' => null,
    'width' => 'sm',
])

<div
    x-data="{ isOpen: false }"
    @if ($id)
        x-on:{{ $closeEventName }}.window="if ($event.detail.id === '{{ $id }}') isOpen = false"
        x-on:{{ $openEventName }}.window="if ($event.detail.id === '{{ $id }}') isOpen = true"
    @endif
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($heading)
        aria-labelledby="{{ "{$id}.heading" }}"
    @endif
    role="dialog"
    aria-modal="true"
    class="{{ $displayClasses }} filament-forms-modal"
>
    {{ $trigger }}

    <div
        x-show="isOpen"
        x-transition:enter="ease duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-40 flex items-center min-h-screen p-4 overflow-y-auto transition"
    >
        <button
            x-on:click="isOpen = false"
            type="button"
            aria-hidden="true"
            class="fixed inset-0 w-full h-full bg-black/50 focus:outline-none filament-forms-modal-close-overlay"
        ></button>

        <div
            x-show="isOpen"
            x-trap="isOpen"
            x-on:keydown.window.escape="isOpen = false"
            x-transition:enter="ease duration-300"
            x-transition:enter-start="translate-y-8"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="ease duration-300"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-8"
            x-cloak
            {{ $attributes->class(['relative w-full mt-auto md:mb-auto cursor-pointer']) }}
        >
            <div
                @class([
                    'w-full mx-auto p-2 space-y-2 bg-white rounded-xl cursor-default filament-forms-modal-window',
                    'dark:bg-gray-800' => config('forms.dark_mode'),
                    'max-w-xs' => $width === 'xs',
                    'max-w-sm' => $width === 'sm',
                    'max-w-md' => $width === 'md',
                    'max-w-lg' => $width === 'lg',
                    'max-w-xl' => $width === 'xl',
                    'max-w-2xl' => $width === '2xl',
                    'max-w-3xl' => $width === '3xl',
                    'max-w-4xl' => $width === '4xl',
                    'max-w-5xl' => $width === '5xl',
                    'max-w-6xl' => $width === '6xl',
                    'max-w-7xl' => $width === '7xl',
                ])
            >
                @if ($header)
                    <div class="px-4 py-2 filament-forms-modal-header">
                        {{ $header }}
                    </div>
                @endif

                @if ($header && ($actions || $heading || $slot->isNotEmpty() || $subheading))
                    <x-forms::hr />
                @endif

                <div class="space-y-2 filament-forms-modal-content">
                    @if ($heading || $subheading)
                        <div @class([
                            'p-4 space-y-2 text-center',
                            'dark:text-white' => config('forms.dark_mode'),
                        ])>
                            @if ($heading)
                                <x-forms::modal.heading :id="$id . '.heading'">
                                    {{ $heading }}
                                </x-forms::modal.heading>
                            @endif

                            @if ($subheading)
                                <x-forms::modal.subheading>
                                    {{ $subheading }}
                                </x-forms::modal.subheading>
                            @endif
                        </div>
                    @endif

                    @if ($slot->isNotEmpty())
                        <div class="px-4 py-2 space-y-4">
                            {{ $slot }}
                        </div>
                    @endif

                    {{ $actions }}
                </div>

                @if ($footer && ($actions || $heading || $slot->isNotEmpty() || $subheading))
                    <x-forms::hr />
                @endif

                @if ($footer)
                    <div class="px-4 py-2 filament-forms-modal-footer">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
