<fieldset
    {!! $getId() ? "id=\"{$getId()}\"" : null !!}
    {{ $attributes->merge($getExtraAttributes())->class(['rounded-xl shadow-sm border border-gray-300 p-6 dark:border-dark-600 dark:text-dark-200', 'filament-forms-fieldset-component']) }}
>
    <legend class="text-sm leading-tight font-medium px-2 -ml-2">
        {{ $getLabel() }}
    </legend>

    {{ $getChildComponentContainer() }}
</fieldset>
