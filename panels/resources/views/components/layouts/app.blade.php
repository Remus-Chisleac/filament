@php
    $navigation = filament()->getNavigation();
@endphp

<x-filament::layouts.base :livewire="$livewire">
    <div class="fi-app-layout flex min-h-full w-full overflow-x-clip">
        <div
            x-data="{}"
            x-cloak
            x-show="$store.sidebar.isOpen"
            x-transition.opacity.500ms
            x-on:click="$store.sidebar.close()"
            class="fi-sidebar-close-overlay fixed inset-0 z-30 h-full w-full bg-gray-900/50 lg:hidden"
        ></div>

        <x-filament::layouts.app.sidebar :navigation="$navigation" />

        <div
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-data="{}"
                x-bind:class="{
                    'lg:ps-[--collapsed-sidebar-width]': ! $store.sidebar.isOpen,
                    'fi-main-sidebar-open lg:ps-[--sidebar-width]': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @elseif (filament()->isSidebarFullyCollapsibleOnDesktop())
                x-data="{}"
                x-bind:class="{
                    'fi-main-sidebar-open lg:ps-[--sidebar-width]': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @endif
            @class([
                'fi-main w-screen flex-1 flex-col space-y-6 lg:pe-0',
                'hidden h-full transition-all' => filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop(),
                'flex lg:ps-[--sidebar-width]' => ! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation()),
            ])
        >
            <x-filament::layouts.app.topbar :navigation="$navigation" />

            <div
                @class([
                    'fi-main-content mx-auto w-full flex-1 px-4 md:px-6 lg:px-8',
                    match ($maxContentWidth ?? filament()->getMaxContentWidth() ?? '7xl') {
                        'xl' => 'max-w-xl',
                        '2xl' => 'max-w-2xl',
                        '3xl' => 'max-w-3xl',
                        '4xl' => 'max-w-4xl',
                        '5xl' => 'max-w-5xl',
                        '6xl' => 'max-w-6xl',
                        '7xl' => 'max-w-7xl',
                        'full' => 'max-w-full',
                        default => $maxContentWidth,
                    },
                ])
            >
                {{ \Filament\Support\Facades\FilamentView::renderHook('content.start') }}

                {{ $slot }}

                {{ \Filament\Support\Facades\FilamentView::renderHook('content.end') }}
            </div>

            <div class="shrink-0 py-4">
                <x-filament::footer />
            </div>
        </div>
    </div>
</x-filament::layouts.base>
