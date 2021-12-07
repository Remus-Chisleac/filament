<?php

namespace Filament\Pages\Actions\Concerns;

trait CanBeHidden
{
    protected bool $isHidden = false;

    public function hidden(bool $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }
}
