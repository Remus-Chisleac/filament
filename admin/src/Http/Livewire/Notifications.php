<?php

namespace Filament\Http\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Http\Livewire\Notifications as BaseComponent;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class Notifications extends BaseComponent
{
    public function getUser(): Model | Authenticatable
    {
        return Filament::auth()->user();
    }

    public function hasDatabaseNotifications(): bool
    {
        return config('filament.database_notifications.enabled');
    }

    public function getPollingInterval(): ?string
    {
        return config('filament.database_notifications.polling_interval');
    }

    public function getDatabaseNotificationsButton(): View
    {
        return view('filament::components.layouts.app.topbar.database-notifications-button');
    }
}
