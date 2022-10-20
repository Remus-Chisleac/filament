<?php

namespace Filament\Forms\Components\Actions\Modal\Actions;

use Filament\Actions\Modal\Actions\Action as BaseAction;

class Action extends BaseAction
{
    protected string $view = 'filament-forms::components.actions.modal.actions.button-action';

    public function button(): static
    {
        $this->view('filament-forms::components.actions.modal.actions.button-action');

        return $this;
    }
}
