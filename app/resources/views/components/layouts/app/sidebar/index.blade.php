<aside
    x-data="{}"
    @if (filament()->isSidebarCollapsibleOnDesktop())
        x-cloak
        x-bind:class="$store.sidebar.isOpen ? 'filament-sidebar-open translate-x-0 max-w-[20em] lg:max-w-[var(--sidebar-width)]' : '-translate-x-full lg:translate-x-0 lg:max-w-[var(--collapsed-sidebar-width)] rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @else
        x-cloak="-lg"
        x-bind:class="$store.sidebar.isOpen ? 'filament-sidebar-open translate-x-0' : '-translate-x-full lg:translate-x-0 rtl:lg:-translate-x-0 rtl:translate-x-full'"
    @endif
    @class([
        'filament-sidebar fixed inset-y-0 left-0 z-20 flex h-screen w-[var(--sidebar-width)] flex-col overflow-hidden bg-white transition-all rtl:left-auto rtl:right-0 dark:bg-gray-800 lg:bg-transparent lg:dark:bg-transparent lg:z-0',
        'lg:translate-x-0' => ! filament()->isSidebarCollapsibleOnDesktop(),
    ])
>
    <header class="filament-sidebar-header border-b h-[4rem] shrink-0 flex items-center justify-center relative bg-white dark:bg-gray-800 dark:border-gray-700 lg:border-r rtl:lg:border-r-0 rtl:lg:border-l">
        <div
            @class([
                'flex items-center justify-center px-6 w-full',
                'lg:px-4' => filament()->isSidebarCollapsibleOnDesktop() && (! filament()->isSidebarFullyCollapsibleOnDesktop()),
            ])
            x-show="$store.sidebar.isOpen || @js(! filament()->isSidebarCollapsibleOnDesktop()) || @js(filament()->isSidebarFullyCollapsibleOnDesktop())"
            x-transition:enter="lg:transition delay-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            @if (filament()->isSidebarCollapsibleOnDesktop() && (! filament()->isSidebarFullyCollapsibleOnDesktop()))
                <button
                    type="button"
                    class="filament-sidebar-collapse-button shrink-0 hidden lg:flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none"
                    x-bind:aria-label="$store.sidebar.isOpen ? '{{ __('filament::layout.buttons.sidebar.collapse.label') }}' : '{{ __('filament::layout.buttons.sidebar.expand.label') }}'"
                    x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                    x-transition:enter="lg:transition delay-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                >
                    <x-filament::icon
                        alias="app::sidebar.buttons.collapse"
                        color="text-primary-500"
                        size="h-6 w-6"
                    >
                        <svg class="h-full w-full" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.25 7.5L16 12L20.25 16.5M3.75 12H12M3.75 17.25H16M3.75 6.75H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </x-filament::icon>
                </button>
            @endif

            <a
                href="{{ filament()->getHomeUrl() }}"
                data-turbo="false"
                @class([
                    'block w-full',
                    'lg:ml-3' => filament()->isSidebarCollapsibleOnDesktop() && (! filament()->isSidebarFullyCollapsibleOnDesktop()),
                ])
            >
                <x-filament::brand />
            </a>
        </div>

        @if (filament()->isSidebarCollapsibleOnDesktop())
            <button
                type="button"
                class="filament-sidebar-close-button shrink-0 flex items-center justify-center w-10 h-10 text-primary-500 rounded-full hover:bg-gray-500/5 focus:bg-primary-500/10 focus:outline-none"
                x-bind:aria-label="$store.sidebar.isOpen ? '{{ __('filament::layout.buttons.sidebar.collapse.label') }}' : '{{ __('filament::layout.buttons.sidebar.expand.label') }}'"
                x-on:click.stop="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
                x-show="(! $store.sidebar.isOpen) && @js(! filament()->isSidebarFullyCollapsibleOnDesktop())"
                x-transition:enter="lg:transition delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
            >
                <x-filament::icon
                    name="heroicon-o-bars-3"
                    alias="app::sidebar.buttons.toggle"
                    size="h-6 w-6"
                />
            </button>
        @endif
    </header>

    <nav class="flex-1 py-6 overflow-x-hidden overflow-y-auto filament-sidebar-nav">
        {{ filament()->renderHook('sidebar.start') }}

        @if (filament()->hasRoutableTenancy())
            <div class="px-6 space-y-6 mb-6">
                <x-filament::tenant-menu />

                <div class="border-t -mr-6 rtl:-mr-auto rtl:-ml-6 dark:border-gray-700"></div>
            </div>
        @endif

        @php
            $navigation = filament()->getNavigation();

            $collapsedNavigationGroupLabels = collect($navigation)
                ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                ->values();
        @endphp

        <script>
            if (JSON.parse(localStorage.getItem('collapsedGroups')) === null) {
                localStorage.setItem('collapsedGroups', JSON.stringify(@js($collapsedNavigationGroupLabels)))
            }
        </script>

        <ul class="px-6 space-y-6">
            @foreach ($navigation as $group)
                <x-filament::layouts.app.sidebar.group
                    :label="$group->getLabel()"
                    :icon="$group->getIcon()"
                    :collapsible="$group->isCollapsible()"
                    :items="$group->getItems()"
                />
            @endforeach
        </ul>

        {{ filament()->renderHook('sidebar.end') }}
    </nav>

    {{ filament()->renderHook('sidebar.footer') }}
</aside>
