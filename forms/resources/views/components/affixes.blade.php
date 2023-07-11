@props([
    'disabled' => false,
    'prefix' => null,
    'prefixActions' => [],
    'prefixIcon' => null,
    'prefixIconAlias' => null,
    'statePath' => null,
    'suffix' => null,
    'suffixActions' => [],
    'suffixIcon' => null,
    'suffixIconAlias' => null,
])

@php
    $ringClasses = \Illuminate\Support\Arr::toCssClasses([
        'ring-gray-950/10 dark:ring-white/20',
        'focus-within:ring-primary-600 dark:focus-within:ring-primary-600' => ! $disabled,
    ]);

    $errorRingClasses = \Illuminate\Support\Arr::toCssClasses([
        'ring-danger-600 dark:ring-danger-400',
        'focus-within:ring-danger-600 dark:focus-within:ring-danger-400' => ! $disabled,
    ]);

    $affixesClasses = 'flex items-center gap-x-3 border-gray-950/10 px-3 dark:border-white/20';

    $affixActionsClasses = '-mx-2.5 flex';

    $affixIconClasses = 'filament-input-affix-icon h-5 w-5 text-gray-400 dark:text-gray-500';

    $affixLabelClasses = 'filament-input-affix-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400';

    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Forms\Components\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Forms\Components\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );
@endphp

<div
    @if ($statePath)
        x-bind:class="
            @js($statePath) in $wire.__instance.snapshot.memo.errors
                ? '{{ $errorRingClasses }}'
                : '{{ $ringClasses }}'
        "
    @endif
    {{
        $attributes->class([
            'filament-forms-affix-container flex rounded-lg shadow-sm ring-1 transition duration-75',
            $ringClasses => ! $statePath,
            'bg-gray-50 dark:bg-gray-950' => $disabled,
            'bg-white focus-within:ring-2 dark:bg-gray-900' => ! $disabled,
        ])
    }}
>
    @if (count($prefixActions) || $prefixIcon || filled($prefix))
        <div
            @class([
                $affixesClasses,
                'border-e',
            ])
        >
            @if (count($prefixActions))
                <div @class([$affixActionsClasses])>
                    @foreach ($prefixActions as $prefixAction)
                        {{ $prefixAction }}
                    @endforeach
                </div>
            @endif

            @if ($prefixIcon)
                <x-filament::icon
                    :alias="$prefixIconAlias"
                    :name="$prefixIcon"
                    :class="$affixIconClasses"
                />
            @endif

            @if (filled($prefix))
                <span @class([$affixLabelClasses])>
                    {{ $prefix }}
                </span>
            @endif
        </div>
    @endif

    <div class="min-w-0 flex-1">
        {{ $slot }}
    </div>

    @if (count($suffixActions) || $suffixIcon || filled($suffix))
        <div
            @class([
                $affixesClasses,
                'border-s',
            ])
        >
            @if (filled($suffix))
                <span @class([$affixLabelClasses])>
                    {{ $suffix }}
                </span>
            @endif

            @if ($suffixIcon)
                <x-filament::icon
                    :alias="$suffixIconAlias"
                    :name="$suffixIcon"
                    :class="$affixIconClasses"
                />
            @endif

            @if (count($suffixActions))
                <div @class([$affixActionsClasses])>
                    @foreach ($suffixActions as $suffixAction)
                        {{ $suffixAction }}
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
