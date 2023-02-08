<?php

namespace Filament\Infolists\Components\Concerns;

use BackedEnum;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasState
{
    protected mixed $defaultState = null;

    protected ?Closure $getStateUsing = null;

    protected ?string $statePath = null;

    protected string | Closure | null $separator = null;

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function default(mixed $state): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState(): mixed
    {
        return $this->evaluate($this->defaultState, exceptParameters: ['state']);
    }

    public function getState(): mixed
    {
        $state = $this->getStateUsing ? $this->evaluate(
            $this->getStateUsing,
            exceptParameters: ['state'],
        ) : data_get($this->getContainer()->getState(), $this->getStatePath());

        if (
            interface_exists(BackedEnum::class) &&
            ($state instanceof BackedEnum) &&
            property_exists($state, 'value')
        ) {
            $state = $state->value;
        }

        if (is_string($state) && ($separator = $this->getSeparator())) {
            $state = explode($separator, $state);
            $state = (count($state) === 1 && blank($state[0])) ?
                [] :
                $state;
        }

        if ($state === null) {
            $state = value($this->getDefaultState());
        }

        return $state;
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    public function getRecord(): ?Model
    {
        return $this->getContainer()->getRecord();
    }

    public function hasStatePath(): bool
    {
        return filled($this->statePath);
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $pathComponents = [];

        if ($isAbsolute && ($containerStatePath = $this->getContainer()->getStatePath())) {
            $pathComponents[] = $containerStatePath;
        }

        if ($this->hasStatePath()) {
            $pathComponents[] = $this->statePath;
        }

        return implode('.', $pathComponents);
    }
}
