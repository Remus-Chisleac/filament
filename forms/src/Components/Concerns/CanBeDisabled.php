<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Arr;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function disabledOn(string | array $contexts): static
    {
        $this->hidden(static function (string $context, HasForms $livewire) use ($contexts): bool {
            foreach (Arr::wrap($contexts) as $hiddenContext) {
                if ($hiddenContext === $context || $livewire instanceof $hiddenContext) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->evaluate($this->isDisabled) || $this->getContainer()->isDisabled();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
