@props([
    'alignment' => 'start',
    'ariaLabelledby' => null,
    'closeButton' => \Filament\Support\View\Components\Modal::$hasCloseButton,
    'closeByClickingAway' => \Filament\Support\View\Components\Modal::$isClosedByClickingAway,
    'closeEventName' => 'close-modal',
    'description' => null,
    'displayClasses' => 'inline-block',
    'footer' => null,
    'footerActions' => [],
    'footerActionsAlignment' => 'start',
    'header' => null,
    'heading' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconColor' => 'primary',
    'id' => null,
    'openEventName' => 'open-modal',
    'slideOver' => false,
    'stickyFooter' => false,
    'trigger' => null,
    'visible' => true,
    'width' => 'sm',
])

<div
    @if ($ariaLabelledby)
        aria-labelledby="{{ $ariaLabelledby }}"
    @elseif ($heading)
        aria-labelledby="{{ "{$id}.heading" }}"
    @endif
    aria-modal="true"
    role="dialog"
    x-data="{
        isOpen: false,

        livewire: null,

        close: function () {
            this.isOpen = false

            this.$refs.modalContainer.dispatchEvent(
                new CustomEvent('modal-closed', { id: '{{ $id }}' }),
            )
        },

        open: function () {
            this.isOpen = true

            this.$refs.modalContainer.dispatchEvent(
                new CustomEvent('modal-opened', { id: '{{ $id }}' }),
            )
        },
    }"
    @if ($id)
        x-on:{{ $closeEventName }}.window="if ($event.detail.id === '{{ $id }}') close()"
        x-on:{{ $openEventName }}.window="if ($event.detail.id === '{{ $id }}') open()"
    @endif
    x-trap.noscroll="isOpen"
    wire:ignore.self
    @class([
        'fi-modal',
        $displayClasses,
    ])
>
    {{ $trigger }}

    <div
        x-cloak
        x-show="isOpen"
        x-transition.duration.300ms.opacity
        @class([
            'fixed inset-0 z-40 min-h-full overflow-y-auto overflow-x-hidden transition',
            'flex items-center' => ! $slideOver,
        ])
    >
        <div
            aria-hidden="true"
            @if ($closeByClickingAway)
                @if (filled($id))
                    x-on:click="$dispatch('{{ $closeEventName }}', { id: '{{ $id }}' })"
                @else
                    x-on:click="close()"
                @endif
            @endif
            @class([
                'fi-modal-close-overlay fixed inset-0 bg-gray-950/50 dark:bg-gray-950/75',
                'cursor-pointer' => $closeByClickingAway,
            ])
            style="will-change: transform"
        ></div>

        <div
            x-cloak
            x-ref="modalContainer"
            {{
                $attributes->class([
                    'pointer-events-none relative w-full transition',
                    'my-auto p-4' => ! $slideOver,
                ])
            }}
        >
            <div
                x-cloak
                x-data="{ isShown: false }"
                x-init="
                    $nextTick(() => {
                        isShown = isOpen
                        $watch('isOpen', () => (isShown = isOpen))
                    })
                "
                @if (filled($id))
                    x-on:keydown.window.escape="$dispatch('{{ $closeEventName }}', { id: '{{ $id }}' })"
                @else
                    x-on:keydown.window.escape="close()"
                @endif
                x-show="isShown"
                x-transition:enter="duration-300"
                x-transition:leave="duration-300"
                @if ($slideOver)
                    x-transition:enter-start="translate-x-full rtl:-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full rtl:-translate-x-full"
                @elseif ($width !== 'screen')
                    x-transition:enter-start="scale-95"
                    x-transition:enter-end="scale-100"
                    x-transition:leave-start="scale-95"
                    x-transition:leave-end="scale-100"
                @endif
                @class([
                    'fi-modal-window pointer-events-auto w-full cursor-default bg-white shadow-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
                    'relative' => $width !== 'screen',
                    'fi-modal-slide-over-window ms-auto h-screen overflow-y-auto' => $slideOver,
                    'mx-auto rounded-xl' => ! ($slideOver || ($width === 'screen')),
                    'hidden' => ! $visible,
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
                    'fixed inset-0' => $width === 'screen',
                ])
            >
                <div
                    @class([
                        'relative flex h-full flex-col' => ($width === 'screen') || $slideOver,
                    ])
                >
                    @if ($heading || $header)
                        <div
                            @class([
                                'fi-modal-header flex px-6 pt-6',
                                'mb-6' => \Filament\Support\is_slot_empty($slot),
                                match ($alignment) {
                                    'left', 'start' => 'gap-x-5',
                                    'center' => 'flex-col',
                                },
                            ])
                        >
                            @if ($header)
                                {{ $header }}
                            @else
                                @if ($icon)
                                    <div
                                        @class([
                                            'mb-5 flex items-center justify-center' => $alignment === 'center',
                                        ])
                                    >
                                        <div
                                            @class([
                                                'rounded-full bg-custom-100 dark:bg-custom-500/20',
                                                match ($alignment) {
                                                    'left', 'start' => 'p-2',
                                                    'center' => 'p-3',
                                                },
                                            ])
                                            style="{{ \Filament\Support\get_color_css_variables($iconColor, shades: [100, 500]) }}"
                                        >
                                            <x-filament::icon
                                                :alias="$iconAlias"
                                                :icon="$icon"
                                                :style="\Filament\Support\get_color_css_variables($iconColor, shades: [400, 600])"
                                                class="fi-modal-icon h-6 w-6 text-custom-600 dark:text-custom-400"
                                            />
                                        </div>
                                    </div>
                                @endif

                                <div
                                    @class([
                                        'text-center' => $alignment === 'center',
                                    ])
                                >
                                    <x-filament::modal.heading>
                                        {{ $heading }}
                                    </x-filament::modal.heading>

                                    @if ($description)
                                        <p
                                            class="fi-modal-description mt-2 text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {{ $description }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($closeButton)
                        <div
                            @class([
                                'absolute',
                                'end-6 top-6' => $slideOver,
                                'end-4 top-4' => ! $slideOver,
                            ])
                        >
                            <x-filament::icon-button
                                color="gray"
                                icon="heroicon-o-x-mark"
                                icon-alias="modal.close-button"
                                icon-size="lg"
                                :label="__('filament-support::components/modal.actions.close.label')"
                                tabindex="-1"
                                :x-on:click="filled($id) ? '$dispatch(' . \Illuminate\Support\Js::from($closeEventName) . ', { id: ' . \Illuminate\Support\Js::from($id) . ' })' : 'close()'"
                                class="fi-modal-close-btn -m-1.5"
                            />
                        </div>
                    @endif

                    @if (! \Filament\Support\is_slot_empty($slot))
                        <div
                            @class([
                                'fi-modal-content flex flex-col gap-y-4 py-6',
                                'flex-1' => ($width === 'screen') || $slideOver,
                                'pe-6 ps-[5.25rem]' => $icon && ($alignment === 'start'),
                                'px-6' => ! ($icon && ($alignment === 'start')),
                            ])
                        >
                            {{ $slot }}
                        </div>
                    @endif

                    @if (count($footerActions) || (! \Filament\Support\is_slot_empty($footer)))
                        <div
                            @class([
                                'fi-modal-footer w-full',
                                'pe-6 ps-[5.25rem]' => $icon && ($alignment === 'start') && ($footerActionsAlignment !== 'center') && (! $stickyFooter),
                                'px-6' => ! ($icon && ($alignment === 'start') && ($footerActionsAlignment !== 'center') && (! $stickyFooter)),
                                'sticky bottom-0 rounded-b-xl border-t border-gray-100 bg-white py-5 dark:border-gray-800 dark:bg-gray-900' => $stickyFooter,
                                'pb-6' => ! $stickyFooter,
                                'mt-6' => (! $stickyFooter) && \Filament\Support\is_slot_empty($slot) && (! $slideOver),
                                'mt-auto' => $slideOver,
                            ])
                        >
                            @if (! \Filament\Support\is_slot_empty($footer))
                                {{ $footer }}
                            @elseif (count($footerActions))
                                <div
                                    @class([
                                        'fi-modal-footer-actions gap-3',
                                        match ($footerActionsAlignment) {
                                            'center' => 'flex flex-col-reverse sm:grid sm:grid-cols-[repeat(auto-fit,minmax(0,1fr))]',
                                            'end', 'right' => 'flex flex-row-reverse flex-wrap items-center',
                                            'left', 'start' => 'flex flex-wrap items-center',
                                        },
                                    ])
                                >
                                    @foreach ($footerActions as $action)
                                        {{ $action }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
