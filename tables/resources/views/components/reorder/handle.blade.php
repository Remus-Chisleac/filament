<button {{
    $attributes
        ->merge([
            'type' => 'button',
        ], escape: false)
        ->class(['filament-tables-reorder-handle text-gray-500 cursor-move transition group-hover:text-primary-500 dark:text-gray-400 dark:group-hover:text-primary-400'])
}}>
    <x-filament::icon
        name="heroicon-o-bars-3"
        alias="filament-tables::reorder.handle"
        size="h-4 w-4"
        class="block"
    />
</button>
