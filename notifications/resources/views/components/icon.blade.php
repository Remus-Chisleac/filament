@props([
    'icon',
    'color',
])

<x-filament::icon
    :name="$icon"
    alias="filament-notifications::notification"
    :color="match ($color) {
        'danger' => 'text-danger-400',
        'primary' => 'text-primary-400',
        'secondary' => 'text-gray-400',
        'success' => 'text-success-400',
        'warning' => 'text-warning-400',
        default => $color,
    }"
    size="h-6 w-6"
    class="filament-notifications-notification-icon"
/>
