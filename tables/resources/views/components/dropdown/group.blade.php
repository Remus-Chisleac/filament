<x-filament-support::dropdown.group
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :dark-mode="config('tables.dark_mode')"
>
    {{ $slot }}
</x-filament-support::dropdown.group>
