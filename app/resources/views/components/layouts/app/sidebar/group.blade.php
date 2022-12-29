@props([
    'parentGroup' => null,
    'collapsible' => true,
    'icon' => null,
    'items' => [],
    'label' => null,
])

<li
    x-data="{ label: {{ \Illuminate\Support\Js::from((filled($parentGroup) ? "{$parentGroup}." : null) . $label) }} }"
    class="filament-sidebar-group"
    @if (filled($parentGroup))
        x-bind:class="{{ config('filament.layout.sidebar.is_collapsible_on_desktop') ? '$store.sidebar.isOpen' : 'true' }} ? 'ml-11 pr-3 pt-3' : 'hidden'"
    @endif
>
    @if ($label)
        <button
            @if ($collapsible)
                x-on:click.prevent="$store.sidebar.toggleCollapsedGroup(label)"
            @endif
            @if (\Filament\Navigation\Sidebar::$isCollapsibleOnDesktop)
                x-show="$store.sidebar.isOpen"
            @endif
            class="flex items-center justify-between w-full"
        >
            <div class="flex items-center gap-4">
                @if ($icon)
                    <x-filament::icon
                        :name="$icon"
                        alias="app::sidebar.group"
                        color="text-gray-600 dark:text-gray-300"
                        size="h-3 w-3"
                        class="ml-1 flex-shrink-0"
                    />
                @endif

                <p class="flex-1 font-bold uppercase text-xs tracking-wider text-primary-600 dark:text-primary-500">
                    {{ $label }}
                </p>
            </div>

            @if ($collapsible)
                <x-filament::icon
                    name="heroicon-m-chevron-down"
                    alias="app::sidebar.group.collapse"
                    size="h-3 w-3"
                    class="text-gray-600 transition dark:text-gray-300"
                    x-bind:class="$store.sidebar.groupIsCollapsed(label) || '-rotate-180'"
                    x-cloak
                />
            @endif
        </button>
    @endif

    <ul
        x-show="! ($store.sidebar.groupIsCollapsed(label) && {{ \Filament\Navigation\Sidebar::$isCollapsibleOnDesktop ? '$store.sidebar.isOpen' : 'true' }})"
        x-collapse.duration.200ms
        @class([
            'text-sm space-y-1 -mx-3',
            'mt-2' => $label,
        ])
    >
        @foreach ($items as $item)
            @if ($item instanceof \Filament\Navigation\NavigationItem)
                <x-filament::layouts.app.sidebar.item
                    :active="$item->isActive()"
                    :icon="$item->getIcon()"
                    :active-icon="$item->getActiveIcon()"
                    :url="$item->getUrl()"
                    :badge="$item->getBadge()"
                    :badgeColor="$item->getBadgeColor()"
                    :shouldOpenUrlInNewTab="$item->shouldOpenUrlInNewTab()"
                >
                    {{ $item->getLabel() }}
                </x-filament::layouts.app.sidebar.item>
            @elseif ($item instanceof \Filament\Navigation\NavigationGroup)
                <x-filament::layouts.app.sidebar.group
                    :label="$item->getLabel()"
                    :icon="$item->getIcon()"
                    :collapsible="$item->isCollapsible()"
                    :items="$item->getItems()"
                    :parentGroup="(filled($parentGroup) ? ('$parentGroup' . '.') : null) . $label"
                />
            @endif
        @endforeach
    </ul>
</li>
