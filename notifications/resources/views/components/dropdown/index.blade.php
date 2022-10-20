@captureSlots([
    'trigger',
])

<x-filament-support::dropdown
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($slots)"
    :dark-mode="config('filament-notifications.dark_mode')"
>
    {{ $slot }}
</x-filament-support::dropdown>
