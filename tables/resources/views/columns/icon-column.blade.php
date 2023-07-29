<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-icon flex flex-wrap gap-1.5',
                'px-3 py-4' => ! $isInline(),
                'flex-col' => $isListWithLineBreaks(),
            ])
    }}
>
    @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
        @if ($icon = $getIcon($state))
            @php
                $color = $getColor($state) ?? 'gray';
                $size = $getSize($state) ?? 'lg';
            @endphp

            <x-filament::icon
                :icon="$icon"
                @class([
                    'fi-ta-icon-item',
                    match ($size) {
                        'xs' => 'fi-ta-icon-item-size-xs h-3 w-3',
                        'sm' => 'fi-ta-icon-item-size-sm h-4 w-4',
                        'md' => 'fi-ta-icon-item-size-md h-5 w-5',
                        'lg' => 'fi-ta-icon-item-size-lg h-6 w-6',
                        'xl' => 'fi-ta-icon-item-size-xl h-7 w-7',
                        default => $size,
                    },
                    match ($color) {
                        'gray' => 'text-gray-400 dark:text-gray-500',
                        default => 'text-custom-500 dark:text-custom-400',
                    },
                ])
                @style([
                    \Filament\Support\get_color_css_variables($color, shades: [400, 500]) => $color !== 'gray',
                ])
            />
        @endif
    @endforeach
</div>
