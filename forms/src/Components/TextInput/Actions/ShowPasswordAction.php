<?php

namespace Filament\Forms\Components\TextInput\Actions;

use Filament\Forms\Components\Actions\Action;
use Filament\Support\Facades\FilamentIcon;

class ShowPasswordAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'showPassword';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-forms::components.text_input.actions.show_password.label'));

        $this->icon(FilamentIcon::resolve('forms::components.text-input.actions.show-password') ?? 'heroicon-m-eye');

        $this->extraAttributes([
            'x-show' => '! isPasswordRevealed',
        ]);

        $this->livewireClickHandlerEnabled(false);
    }

    public function getAlpineClickHandler(): string
    {
        return 'isPasswordRevealed = true';
    }
}
