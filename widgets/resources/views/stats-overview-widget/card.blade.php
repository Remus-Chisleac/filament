@php
    $chartColor = $getChartColor() ?? 'gray';
    $descriptionColor = $getDescriptionColor() ?? 'gray';
    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';

    $descriptionIconClasses = \Illuminate\Support\Arr::toCssClasses([
        'fi-wi-stats-overview-card-description-icon h-5 w-5',
        match ($descriptionColor) {
            'gray' => 'text-gray-400 dark:text-gray-500',
            default => 'text-custom-500',
        },
    ]);

    $descriptionIconStyles = \Illuminate\Support\Arr::toCssStyles([
        \Filament\Support\get_color_css_variables($descriptionColor, shades: [500]) => $descriptionColor !== 'gray',
    ]);
@endphp

<{!! $tag !!}
    @if ($url)
        href="{{ $url }}"
        @if ($shouldOpenUrlInNewTab())
            target="_blank"
        @endif
    @endif
    {{
        $getExtraAttributeBag()
            ->class([
                'fi-wi-stats-overview-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
            ])
    }}
>
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-2">
            @if ($icon = $getIcon())
                <x-filament::icon
                    :icon="$icon"
                    class="fi-wi-stats-overview-card-icon h-5 w-5 text-gray-400 dark:text-gray-500"
                />
            @endif

            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $getLabel() }}
            </span>
        </div>

        <div
            class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white"
        >
            {{ $getValue() }}
        </div>

        @if ($description = $getDescription())
            <div class="flex items-center gap-x-1">
                @if ($descriptionIcon && ($descriptionIconPosition === 'before'))
                    <x-filament::icon
                        :icon="$descriptionIcon"
                        :class="$descriptionIconClasses"
                        :style="$descriptionIconStyles"
                    />
                @endif

                <span
                    @class([
                        'fi-wi-stats-overview-card-description text-sm',
                        match ($descriptionColor) {
                            'gray' => 'text-gray-500 dark:text-gray-400',
                            default => 'text-custom-600 dark:text-custom-400',
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables($descriptionColor, shades: [400, 600]) => $descriptionColor !== 'gray',
                    ])
                >
                    {{ $description }}
                </span>

                @if ($descriptionIcon && ($descriptionIconPosition === 'after'))
                    <x-filament::icon
                        :icon="$descriptionIcon"
                        :class="$descriptionIconClasses"
                        :style="$descriptionIconStyles"
                    />
                @endif
            </div>
        @endif
    </div>

    @if ($chart = $getChart())
        <div
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('stats-overview/card/chart', 'filament/widgets') }}"
            wire:ignore
            x-data="statsOverviewCardChart({
                        labels: @js(array_keys($chart)),
                        values: @js(array_values($chart)),
                    })"
            x-ignore
            @class([
                'absolute inset-x-0 bottom-0 overflow-hidden rounded-b-xl',
            ])
            @style([
                \Filament\Support\get_color_css_variables($chartColor, shades: [50, 400, 500]) => $chartColor !== 'gray',
            ])
        >
            <canvas x-ref="canvas" class="h-6"></canvas>

            <span
                x-ref="backgroundColorElement"
                @class([
                    match ($chartColor) {
                        'gray' => 'text-gray-100 dark:text-gray-800',
                        default => 'text-custom-50 dark:text-custom-400/10',
                    },
                ])
                class=""
            ></span>

            <span
                x-ref="borderColorElement"
                @class([
                    match ($chartColor) {
                        'gray' => 'text-gray-400',
                        default => 'text-custom-500 dark:text-custom-400',
                    },
                ])
            ></span>
        </div>
    @endif
</{!! $tag !!}>
