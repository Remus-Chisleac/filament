@php
    $action = $getAction();
    $url = $isDisabled() ? null : $getUrl();

    if (! $action) {
        $clickAction = null;
    } elseif ($shouldOpenModal() || ($action instanceof \Closure)) {
        $clickAction = "mountAction('{$getName()}')";
    } else {
        $clickAction = $action;
    }

   $clickAction = $isDisabled() ? null : $clickAction;
@endphp

<x-filament::button
    :form="$getForm()"
    :tag="((! $action) && $url) ? 'a' : 'button'"
    :wire:click="$clickAction"
    :href="$url"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :type="$canSubmitForm() ? 'submit' : 'button'"
    :color="$getColor()"
    :outlined="$isOutlined()"
    :disabled="$isDisabled()"
    :icon="$getIcon()"
    :icon-position="$getIconPosition()"
    class="filament-page-button-action"
>
    {{ $getLabel() }}
</x-filament::button>
