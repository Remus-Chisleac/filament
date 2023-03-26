<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Concerns\HasColumns;
use Filament\Forms\Concerns\HasStateBindingModifiers;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Illuminate\Database\Eloquent\Model;
use ReflectionParameter;

class Component extends ViewComponent
{
    use Concerns\BelongsToContainer;
    use Concerns\BelongsToModel;
    use Concerns\CanBeConcealed;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanSpanColumns;
    use Concerns\Cloneable;
    use Concerns\HasActions;
    use Concerns\HasChildComponents;
    use Concerns\HasFieldWrapper;
    use Concerns\HasInlineLabel;
    use Concerns\HasId;
    use Concerns\HasLabel;
    use Concerns\HasMaxWidth;
    use Concerns\HasMeta;
    use Concerns\HasState;
    use Concerns\ListensToEvents;
    use HasColumns;
    use HasExtraAttributes;
    use HasStateBindingModifiers;

    protected string $evaluationIdentifier = 'component';

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'context', 'operation' => $this->getContainer()->getOperation(),
            'get' => $this->getGetCallback(),
            'livewire' => $this->getLivewire(),
            'model' => $this->getModel(),
            'record' => $this->getRecord(),
            'set' => $this->getSetCallback(),
            'state' => $this->getState(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }

    public function getKey(): ?string
    {
        return null;
    }
}
