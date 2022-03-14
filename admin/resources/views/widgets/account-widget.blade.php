<x-filament::widget class="filament-account-widget">
    <x-filament::card>
        @php
            $user = \Filament\Facades\Filament::auth()->user();
        @endphp

        <div class="h-12 flex items-center space-x-4 rtl:space-x-reverse">
            <div
                class="w-10 h-10 rounded-full bg-gray-200 bg-cover bg-center"
                style="background-image: url('{{ \Filament\Facades\Filament::getUserAvatarUrl($user) }}')"
            ></div>

            <div>
                <h2 class="text-lg sm:text-xl font-bold tracking-tight">
                    {{ __('filament::widgets/account-widget.welcome', ['user' => \Filament\Facades\Filament::getUserName($user)]) }}
                </h2>

                <form method="POST" action="{{ route('filament.auth.logout') }}" class="text-sm">
                    @csrf

                    <a href="{{ route('filament.auth.logout') }}" @class([
                        'text-gray-600 hover:text-primary-500 focus:outline-none focus:underline',
                        'dark:text-gray-300 dark:hover:text-primary-500' => config('filament.dark_mode'),
                    ]) onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('filament::widgets/account-widget.buttons.logout.label') }}
                    </a>
                </form>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
