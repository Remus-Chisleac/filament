<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Tables\Columns\Contracts\Editable;

class ToggleColumn extends Column implements Editable
{
    use Concerns\CanBeValidated;
    use Concerns\CanSaveState {
        Concerns\CanSaveState::setUp as setUpSaveState;
    }
    use HasToggleColors;
    use HasToggleIcons;

    protected string $view = 'tables::columns.toggle-column';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpSaveState();

        $this->disableClick();

        $this->rules(['boolean']);
    }
}
