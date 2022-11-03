@props([
    'chart' => null,
    'chartColor' => null,
    'color' => null,
    'icon' => null,
    'description' => null,
    'descriptionColor' => null,
    'descriptionIcon' => null,
    'flat' => false,
    'label' => null,
    'tag' => 'div',
    'value' => null,
    'extraAttributes' => [],
])

<{!! $tag !!}
    {{
        $attributes
            ->merge($extraAttributes)
            ->class(['filament-stats-card relative rounded-xl bg-white p-6 shadow ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10'])
    }}
>
    <div @class([
        'space-y-2',
    ])>
        <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm font-medium">
            @if ($icon)
                <x-filament::icon
                    :name="$icon"
                    alias="app::stats.card"
                    color="text-gray-500 dark:text-gray-200"
                    size="h-4 w-4"
                />
            @endif

            <span>{{ $label }}</span>
        </div>

        <div class="text-3xl">
            {{ $value }}
        </div>

        @if ($description)
            <div @class([
                'flex items-center space-x-1 rtl:space-x-reverse text-sm font-medium',
                match ($descriptionColor) {
                    'danger' => 'text-danger-600',
                    'primary' => 'text-primary-600',
                    'secondary' => 'text-secondary-600',
                    'success' => 'text-success-600',
                    'warning' => 'text-warning-600',
                    default => 'text-gray-600',
                },
            ])>
                <span>{{ $description }}</span>

                @if ($descriptionIcon)
                    <x-filament::icon
                        :name="$descriptionIcon"
                        alias="app::stats.card.description"
                        size="h-4 w-4"
                    />
                @endif
            </div>
        @endif
    </div>

    @if ($chart)
        <div
            x-title="filament-stats-card-chart"
            x-data="{
                chart: null,

                labels: {{ json_encode(array_keys($chart)) }},
                values: {{ json_encode(array_values($chart)) }},

                init: function () {
                    this.chart ? this.updateChart() : this.initChart()
                },

                initChart: function () {
                    return this.chart = new Chart(this.$refs.canvas, {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                data: this.values,
                                backgroundColor: getComputedStyle($refs.backgroundColorElement).color,
                                borderColor: getComputedStyle($refs.borderColorElement).color,
                                borderWidth: 2,
                                fill: 'start',
                                tension: 0.5,
                            }],
                        },
                        options: {
                            elements: {
                                point: {
                                    radius: 0,
                                },
                            },
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                            scales: {
                                x:  {
                                    display: false,
                                },
                                y:  {
                                    display: false,
                                },
                            },
                            tooltips: {
                                enabled: false,
                            },
                        },
                    })
                },

                updateChart: function () {
                    this.chart.data.labels = this.labels
                    this.chart.data.datasets[0].data = this.values
                    this.chart.update()
                },
            }"
            class="absolute bottom-0 inset-x-0 rounded-b-xl overflow-hidden"
        >
            <canvas
                wire:ignore
                x-ref="canvas"
                class="h-6"
            >
                <span
                    x-ref="backgroundColorElement"
                    @class([
                        match ($chartColor) {
                            'danger' => 'text-danger-50 dark:text-danger-700',
                            'primary' => 'text-primary-50 dark:text-primary-700',
                            'secondary' => 'text-secondary-50 dark:text-secondary-700',
                            'success' => 'text-success-50 dark:text-success-700',
                            'warning' => 'text-warning-50 dark:text-warning-700',
                            default => 'text-gray-50 dark:text-gray-700',
                        },
                    ])
                ></span>

                <span
                    x-ref="borderColorElement"
                    @class([
                        match ($chartColor) {
                            'danger' => 'text-danger-400',
                            'primary' => 'text-primary-400',
                            'secondary' => 'text-secondary-400',
                            'success' => 'text-success-400',
                            'warning' => 'text-warning-400',
                            default => 'text-gray-400',
                        },
                    ])
                ></span>
            </canvas>
        </div>
    @endif
</{!! $tag !!}>
