<x-filament-widgets::widget class="fi-wi-table">
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Support\View\RenderHook::WIDGETS_TABLE_WIDGET_START, scopes: static::class) }}

    {{ $this->table }}

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Support\View\RenderHook::WIDGETS_TABLE_WIDGET_END, scopes: static::class) }}
</x-filament-widgets::widget>
