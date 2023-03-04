<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Component;

trait Cloneable
{
    protected function cloneComponents(): static
    {
        if (is_array($this->components)) {
            $this->components = array_map(
                fn (Component $component): Component => $component->getClone(),
                $this->components,
            );
        }

        return $this;
    }

    public function getClone(): static
    {
        $clone = clone $this;
        $clone->cloneComponents();

        return $clone;
    }
}
