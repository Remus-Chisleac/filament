@props([
    'actions',
    'alignment' => 'start',
    'fullWidth' => false,
])

@if ($actions instanceof \Illuminate\Contracts\View\View)
    {{ $actions }}
@elseif (is_array($actions))
    @php
        $actions = array_filter(
            $actions,
            fn ($action): bool => $action->isVisible(),
        );
    @endphp

    @if (count($actions))
        <div
            {{
                $attributes->class([
                    'fi-ac',
                    'flex flex-wrap items-center gap-3' => ! $fullWidth,
                    match ($alignment) {
                        'center' => 'justify-center',
                        'end', 'right' => 'flex-row-reverse',
                        default => 'justify-start',
                    } => ! $fullWidth,
                    'grid grid-cols-[repeat(auto-fit,minmax(0,1fr))] gap-2' => $fullWidth,
                ])
            }}
        >
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
@endif
