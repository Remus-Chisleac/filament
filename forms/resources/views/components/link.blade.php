<x-filament-support::link
    :attributes="\Filament\Support\prepare_inherited_attributes($attributes)"
    :dark-mode="config('filament-forms.dark_mode')"
>
    {{ $slot }}
</x-filament-support::link>
