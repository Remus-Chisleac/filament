<?php

namespace Filament\Support\Actions\Modal\Actions;

use Filament\Support\Actions\BaseAction;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\HasColor;
use Filament\Support\Actions\Concerns\HasIcon;
use Filament\Support\Actions\Concerns\HasLabel;
use Filament\Support\Actions\Concerns\HasName;
use Filament\Support\Actions\Concerns\HasView;
use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component;

abstract class Action extends BaseAction
{
    use CanBeOutlined;
    use Concerns\CanCancelAction;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
}
