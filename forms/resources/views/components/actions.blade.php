<div
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-fo-actions-component flex h-full flex-col',
                match ($verticalAlignment = $getVerticalAlignment()) {
                    'center' => 'justify-center',
                    'end' => 'justify-end',
                    'start' => 'justify-start',
                    default => $verticalAlignment,
                },
            ])
    }}
>
    <x-filament-actions::actions
        :actions="$getChildComponentContainer()->getComponents()"
        :alignment="$getAlignment()"
        :full-width="$isFullWidth()"
    />
</div>
