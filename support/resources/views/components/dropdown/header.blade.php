@php
    use Filament\Support\Enums\IconSize;
@endphp

@props([
    'color' => 'gray',
    'icon' => null,
    'iconSize' => IconSize::Medium,
    'tag' => 'div',
])

<{{ $tag }}
    {{
        $attributes
            ->class([
                'fi-dropdown-header flex w-full gap-2 p-3 text-sm',
                is_string($color) ? "fi-dropdown-header-color-{$color}" : null,
            ])
            ->style([
                \Filament\Support\get_color_css_variables(
                    $color,
                    shades: [
                        400,
                        ...(filled($icon) ? [500] : []),
                        600,
                    ],
                    alias: 'dropdown.header',
                ) => $color !== 'gray',
            ])
    }}
>
    @if (filled($icon))
        <x-filament::icon
            :icon="$icon"
            @class([
                'fi-dropdown-header-icon',
                match ($iconSize) {
                    IconSize::Small, 'sm' => 'h-4 w-4',
                    IconSize::Medium, 'md' => 'h-5 w-5',
                    IconSize::Large, 'lg' => 'h-6 w-6',
                    default => $iconSize,
                },
                match ($color) {
                    'gray' => 'text-gray-400 dark:text-gray-500',
                    default => 'text-custom-500 dark:text-custom-400',
                },
            ])
        />
    @endif

    <span
        @class([
            'fi-dropdown-header-label flex-1 truncate text-start',
            match ($color) {
                'gray' => 'text-gray-700 dark:text-gray-200',
                default => 'text-custom-600 dark:text-custom-400',
            },
        ])
    >
        {{ $slot }}
    </span>
</{{ $tag }}>
