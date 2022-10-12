<?php

namespace Filament\Tables\Columns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class IconColumn extends Column
{
    use Concerns\HasColor {
        getColor as getBaseColor;
    }
    use Concerns\HasIcon {
        getIcon as getBaseIcon;
    }
    use Concerns\HasSize;

    protected string $view = 'tables::columns.icon-column';

    protected bool | Closure $isBoolean = false;

    protected string | Closure | null $falseColor = null;

    protected string | Closure | null $falseIcon = null;

    protected string | Closure | null $trueColor = null;

    protected string | Closure | null $trueIcon = null;

    public function boolean(bool | Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function false(string | Closure | null $icon = null, string | Closure | null $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    public function falseColor(string | Closure | null $color): static
    {
        $this->boolean();
        $this->falseColor = $color;

        return $this;
    }

    public function falseIcon(string | Closure | null $icon): static
    {
        $this->boolean();
        $this->falseIcon = $icon;

        return $this;
    }

    public function true(string | Closure | null $icon = null, string | Closure | null $color = null): static
    {
        $this->trueIcon($icon);
        $this->trueColor($color);

        return $this;
    }

    public function trueColor(string | Closure | null $color): static
    {
        $this->boolean();
        $this->trueColor = $color;

        return $this;
    }

    public function trueIcon(string | Closure | null $icon): static
    {
        $this->boolean();
        $this->trueIcon = $icon;

        return $this;
    }

    /**
     * @deprecated Use `icons()` instead.
     */
    public function options(array | Arrayable | Closure $options): static
    {
        $this->icons($options);

        return $this;
    }

    public function getIcon(): ?string
    {
        if ($this->isBoolean()) {
            $state = $this->getState();

            if ($state === null) {
                return null;
            }

            return $state ? $this->getTrueIcon() : $this->getFalseIcon();
        }

        return $this->getBaseIcon();
    }

    public function getColor(): ?string
    {
        if ($this->isBoolean()) {
            $state = $this->getState();

            if ($state === null) {
                return null;
            }

            return $state ? $this->getTrueColor() : $this->getFalseColor();
        }

        return $this->getBaseColor();
    }

    public function getFalseColor(): string
    {
        return $this->evaluate($this->falseColor) ?? 'danger';
    }

    public function getFalseIcon(): string
    {
        return $this->evaluate($this->falseIcon) ?? 'heroicon-o-x-circle';
    }

    public function getTrueColor(): string
    {
        return $this->evaluate($this->trueColor) ?? 'success';
    }

    public function getTrueIcon(): string
    {
        return $this->evaluate($this->trueIcon) ?? 'heroicon-o-check-circle';
    }

    public function isBoolean(): bool
    {
        return (bool) $this->evaluate($this->isBoolean);
    }
}
