<p
    data-validation-error
    {{
        $attributes->class([
            'fi-fo-field-wrapper-error-message text-sm text-danger-600 dark:text-danger-400',
        ])
    }}
>
    {{ $slot }}
</p>
