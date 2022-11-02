@props([
    'active' => false,
    'badge' => null,
    'badgeColor' => null,
])

<span
    x-show="$store.sidebar.isOpen"
    @class([
        'inline-flex items-center justify-center ml-auto rtl:ml-0 rtl:mr-auto min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight rounded-xl whitespace-normal',
        match ($active) {
            true => 'text-white bg-white/20',
            false => match ($badgeColor) {
                'danger' => 'text-danger-700 bg-danger-500/10 dark:text-danger-500',
                'secondary' => 'text-gray-700 bg-gray-500/10 dark:text-gray-500',
                'success' => 'text-success-700 bg-success-500/10 dark:text-success-500',
                'warning' => 'text-warning-700 bg-warning-500/10 dark:text-warning-500',
                'primary', null => 'text-primary-700 bg-primary-500/10 dark:text-primary-500',
                default => $badgeColor,
            },
        },
    ])
>
    {{ $badge }}
</span>
