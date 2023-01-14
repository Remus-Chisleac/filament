@props([
    'action' => null,
    'color' => null,
    'icon' => null,
])

<div {{ $attributes->class([
    'filament-infolists-entry-wrapper-hint flex items-center space-x-2 rtl:space-x-reverse',
    match ($color) {
        'danger' => 'text-danger-500 dark:text-danger-300',
        'gray', null => 'text-gray-500 dark:text-gray-300',
        'secondary' => 'text-secondary-500 dark:text-secondary-300',
        'success' => 'text-success-500 dark:text-success-300',
        'warning' => 'text-warning-500 dark:text-warning-300',
        'primary' => 'text-primary-500 dark:text-primary-300',
        default => $color,
    },
]) }}>
    @if ($slot->isNotEmpty())
        <span class="text-xs leading-tight">
            {{ $slot }}
        </span>
    @endif

    @if ($icon)
        <x-filament::icon
            :name="$icon"
            alias="filament-infolists::entry-wrapper.hint"
            size="h-5 w-5"
        />
    @endif

    @if ($action && (! $action->isHidden()))
        <div class="filament-infolists-entry-wrapper-hint-action">
            {{ $action }}
        </div>
    @endif
</div>
