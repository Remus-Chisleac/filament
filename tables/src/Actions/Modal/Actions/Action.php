<?php

namespace Filament\Tables\Actions\Modal\Actions;

use Filament\Support\Actions\Modal\Actions\Action as BaseAction;

class Action extends BaseAction
{
    protected function setUp(): void
    {
        $this->view ?? $this->button();

        parent::setUp();
    }

    public function button(): static
    {
        $this->view('tables::actions.modal.actions.button-action');

        return $this;
    }
}
