<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function hiddenFor(string $page): static
    {
        $this->hidden(fn ($livewire) => $livewire instanceof $page);

        return $this;
    }

    public function when(bool | Closure $condition = true): static
    {
        $this->visible($condition);

        return $this;
    }

    public function whenTruthy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Closure $get) use ($paths): bool {
            foreach ($paths as $path) {
                if (! $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function whenFalsy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Closure $get) use ($paths): bool {
            foreach ($paths as $path) {
                if (! ! $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function visibleFor(string $page): static
    {
        $this->visible(fn ($livewire) => $livewire instanceof $page);

        return $this;
    }

    public function isHidden(): bool
    {
        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        return ! $this->evaluate($this->isVisible);
    }
}
