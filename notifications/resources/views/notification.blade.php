@php
    $isInline = $isInline();
    $iconColor = $getIconColor();
    $color = $getColor();
@endphp

<x-filament-notifications::notification
    :notification="$notification"
    @class([
        'w-full transition duration-300',
        'max-w-sm rounded-xl bg-white shadow-lg ring-1 ring-gray-900/10 dark:ring-gray-50/10' => ! $isInline,
        'dark:bg-gray-800' => ! $color,
        'dark:bg-gray-700' => $color,
    ])
    :x-transition:enter-start="\Illuminate\Support\Arr::toCssClasses([
        'opacity-0',
        match (static::$horizontalAlignment) {
            'left' => '-translate-x-12',
            'right' => 'translate-x-12',
            'center' => match (static::$verticalAlignment) {
                'top' => '-translate-y-12',
                'bottom' => 'translate-y-12',
            },
        },
    ])"
    x-transition:leave-end="scale-95 opacity-0"
>
    <div
        @class([
            'flex gap-3 w-full',
            'py-2 pl-6 pr-2' => $isInline,
            'p-4 rounded-xl border' => ! $isInline,
            'border-primary-500/50 bg-primary-500/10 dark:bg-primary-500/20' => $color === 'primary',
            'border-secondary-500/40 bg-secondary-500/10 dark:bg-secondary-500/20' => $color === 'secondary',
            'border-danger-500/40 bg-danger-500/10 dark:bg-danger-500/20' => $color === 'danger',
            'border-success-500/40 bg-success-500/10 dark:bg-success-500/20' => $color === 'success',
            'border-warning-500/40 bg-warning-500/10 dark:bg-warning-500/20' => $color === 'warning',
            'border-gray-300 dark:border-gray-700' => $color === null,
        ])
    >
        @if ($icon = $getIcon())
            <x-filament-notifications::icon :icon="$icon" :color="$iconColor" />
        @endif

        <div class="grid flex-1">
            @if ($title = $getTitle())
                <x-filament-notifications::title>
                    {{ str($title)->markdown()->sanitizeHtml()->toHtmlString() }}
                </x-filament-notifications::title>
            @endif

            @if ($date = $getDate())
                <x-filament-notifications::date>
                    {{ $date }}
                </x-filament-notifications::date>
            @endif

            @if ($body = $getBody())
                <x-filament-notifications::body>
                    {{ str($body)->markdown()->sanitizeHtml()->toHtmlString() }}
                </x-filament-notifications::body>
            @endif

            @if ($actions = $getActions())
                <x-filament-notifications::actions :actions="$actions" />
            @endif
        </div>

        <x-filament-notifications::close-button />
    </div>
</x-filament-notifications::notification>
