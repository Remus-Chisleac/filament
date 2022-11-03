@props([
    'active' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'icon',
    'shouldOpenUrlInNewTab' => false,
    'url',
])

<li @class(['filament-sidebar-item overflow-hidden', 'filament-sidebar-item-active' => $active])>
    <a
        href="{{ $url }}"
        {!! $shouldOpenUrlInNewTab ? 'target="_blank"' : '' !!}
        x-on:click="window.matchMedia(`(max-width: 1024px)`).matches && $store.sidebar.close()"
        @if (\Filament\Navigation\Sidebar::$isCollapsibleOnDesktop)
            x-data="{ tooltip: {} }"
            x-init="
                Alpine.effect(() => {
                    if (Alpine.store('sidebar').isOpen) {
                        tooltip = false
                    } else {
                        tooltip = {
                            content: @js($slot->toHtml()),
                            theme: Alpine.store('theme') === 'light' ? 'dark' : 'light',
                            placement: document.dir === 'rtl' ? 'left' : 'right',
                        }
                    }
                })
            "
            x-tooltip.html="tooltip"
        @endif
        @class([
            'flex items-center justify-center gap-3 px-3 py-2 rounded-lg font-medium transition',
            'hover:bg-gray-500/5 focus:bg-gray-500/5 dark:text-gray-300 dark:hover:bg-gray-700' => ! $active,
            'bg-primary-500 text-white' => $active,
        ])
    >
        <x-filament::icon
            :name="($active && $activeIcon) ? $activeIcon : $icon"
            alias="app::sidebar.item"
            size="h-5 w-5"
            class="shrink-0"
        />

        <div class="flex flex-1"
            @if (\Filament\Navigation\Sidebar::$isCollapsibleOnDesktop)
                x-show="$store.sidebar.isOpen"
            @endif
            class="flex flex-1"
        >
            {{ $slot }}
        </div>

        @if (filled($badge))
            <x-filament::layouts.app.sidebar.badge
                :badge="$badge"
                :color="$badgeColor"
                :active="$active"
            />
        @endif
    </a>
</li>
