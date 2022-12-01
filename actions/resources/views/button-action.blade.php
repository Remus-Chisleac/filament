<x-filament-actions::action
    :action="$action"
    component="filament::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    :icon-size="$getIconSize()"
    class="filament-actions-button-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
