<x-filament-support::modal.actions
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :alignment="config('filament-tables.layout.actions.modal.actions.alignment')"
    :dark-mode="config('filament-tables.dark_mode')"
>
    {{ $slot }}
</x-filament-support::modal.actions>
