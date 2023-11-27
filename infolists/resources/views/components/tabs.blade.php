@php
    $isContained = $isContained();
@endphp

<div
    x-cloak
    x-data="{
        tab: @if($isTabPersistedInLocalStorage()) $persist(null).as('{{ $getTabLocalStorageName() }}')  @else null @endif,

        init: function () {
            this.$watch('tab', () => this.updateQueryString())

            if(this.tab === null) {
                this.tab = @js(collect($getChildComponentContainer()->getComponents())
                            ->filter(static fn (\Filament\Infolists\Components\Tabs\Tab $tab): bool => $tab->isVisible())
                            ->map(static fn (\Filament\Infolists\Components\Tabs\Tab $tab) => $tab->getId())
                            ->values()
                            ->get($getActiveTab() - 1))
            }
        },

        updateQueryString: function () {
            if (! @js($isTabPersistedInQueryString())) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(@js($getTabQueryStringKey()), this.tab)

            history.pushState(null, document.title, url.toString())
        },
    }"
    {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->merge($getExtraAlpineAttributes(), escape: false)
            ->class([
                'fi-in-tabs flex flex-col',
                'fi-contained rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10' => $isContained,
            ])
    }}
>
    <x-filament::tabs :contained="$isContained" :label="$getLabel()">
        @foreach ($getChildComponentContainer()->getComponents() as $tab)
            @php
                $tabId = $tab->getId();
            @endphp

            <x-filament::tabs.item
                :alpine-active="'tab === \'' . $tabId . '\''"
                :badge="$tab->getBadge()"
                :badge-color="$tab->getBadgeColor()"
                :icon="$tab->getIcon()"
                :icon-position="$tab->getIconPosition()"
                :x-on:click="'tab = \'' . $tabId . '\''"
            >
                {{ $tab->getLabel() }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    @foreach ($getChildComponentContainer()->getComponents() as $tab)
        {{ $tab }}
    @endforeach
</div>
