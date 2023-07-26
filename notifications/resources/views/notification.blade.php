@php
    $color = $getColor() ?? 'gray';
    $isInline = $isInline();
@endphp

<x-filament-notifications::notification
    :notification="$notification"
    :x-transition:enter-start="
        \Illuminate\Support\Arr::toCssClasses([
            'opacity-0',
            ($this instanceof \Filament\Notifications\Livewire\Notifications)
            ? match (static::$horizontalAlignment) {
                'left' => '-translate-x-12',
                'right' => 'translate-x-12',
                'center' => match (static::$verticalAlignment) {
                    'top' => '-translate-y-12',
                    'bottom' => 'translate-y-12',
                    'center' => null,
                },
            }
            : null,
        ])
    "
    :x-transition:leave-end="
        \Illuminate\Support\Arr::toCssClasses([
            'opacity-0',
            'scale-95' => ! $isInline,
        ])
    "
    @class([
        'w-full transition duration-300',
        ...match ($isInline) {
            true => [],
            false => [
                'max-w-sm rounded-xl bg-white shadow-lg ring-1 dark:bg-gray-900',
                match ($color) {
                    'gray' => 'ring-gray-950/5 dark:ring-white/10',
                    default => 'ring-custom-600/20 dark:ring-custom-400/30',
                },
            ],
        },
    ])
    @style([
        \Filament\Support\get_color_css_variables($color, shades: [400, 600]) => ! ($isInline || $color === 'gray'),
    ])
>
    <div
        @class([
            'flex w-full gap-3 p-4',
            ...match ($isInline) {
                true => [],
                false => [
                    'rounded-xl',
                    match ($color) {
                        'gray' => null,
                        default => 'bg-custom-50 dark:bg-custom-400/10',
                    },
                ],
            },
        ])
        @style([
            \Filament\Support\get_color_css_variables($color, shades: [50, 400]) => ! ($isInline || $color === 'gray'),
        ])
    >
        @if ($icon = $getIcon())
            <x-filament-notifications::icon
                :color="$getIconColor()"
                :icon="$icon"
                :size="$getIconSize()"
            />
        @endif

        <div class="mt-0.5 grid flex-1">
            @if (filled($title = $getTitle()))
                <x-filament-notifications::title>
                    {{ str($title)->sanitizeHtml()->toHtmlString() }}
                </x-filament-notifications::title>
            @endif

            @if (filled($date = $getDate()))
                <x-filament-notifications::date class="mt-1">
                    {{ $date }}
                </x-filament-notifications::date>
            @endif

            @if (filled($body = $getBody()))
                <x-filament-notifications::body class="mt-1">
                    {{ str($body)->sanitizeHtml()->toHtmlString() }}
                </x-filament-notifications::body>
            @endif

            @if ($actions = $getActions())
                <x-filament-notifications::actions
                    :actions="$actions"
                    class="mt-3"
                />
            @endif
        </div>

        <x-filament-notifications::close-button />
    </div>
</x-filament-notifications::notification>
