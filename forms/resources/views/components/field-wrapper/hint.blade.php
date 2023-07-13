@props([
    'actions' => [],
    'color' => 'gray',
    'icon' => null,
])

<div
    {{
        $attributes
            ->class([
                'fi-fo-field-wrp-hint flex items-center gap-x-3 text-sm',
                match ($color) {
                    'gray' => 'text-gray-500',
                    default => 'text-custom-600 dark:text-custom-400',
                },
            ])
            ->style([
                \Filament\Support\get_color_css_variables($color, shades: [400, 500, 600]),
            ])
    }}
>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @endif

    @if ($icon)
        <x-filament::icon
            :name="$icon"
            @class([
                'fi-fo-field-wrp-hint-icon h-5 w-5',
                match ($color) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'text-custom-500 dark:text-custom-400',
                },
            ])
        />
    @endif

    @if (count($actions))
        <div class="fi-fo-field-wrp-hint-action -m-1.5 flex items-center">
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
