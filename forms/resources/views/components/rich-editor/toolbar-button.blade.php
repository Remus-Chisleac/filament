<button
    {{
        $attributes
            ->merge([
                'type' => 'button',
            ], escape: false)
            ->class(['fi-fo-rich-editor-toolbar-btn h-full cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm font-medium text-gray-800 shadow-sm transition duration-200 hover:bg-gray-100 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white'])
    }}
>
    {{ $slot }}
</button>
