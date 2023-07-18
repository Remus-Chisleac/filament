@props([
    'form',
    'indicatorsCount' => null,
    'maxHeight' => null,
    'triggerAction',
    'width' => 'xs',
])

<x-filament::dropdown
    :max-height="$maxHeight"
    placement="bottom-end"
    shift
    :width="$width"
    wire:key="{{ $this->getId() }}.table.filters"
    {{ $attributes->class(['fi-ta-filters']) }}
>
    <x-slot name="trigger">
        {{ $triggerAction->badge($indicatorsCount) }}
    </x-slot>

    <x-filament-tables::filters :form="$form" class="p-4" />
</x-filament::dropdown>
