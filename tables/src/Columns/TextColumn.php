<?php

namespace Filament\Tables\Columns;

use BackedEnum;
use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Arrayable;
use stdClass;

class TextColumn extends Column
{
    use Concerns\CanBeCopied;
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasDescription;
    use Concerns\HasFontFamily;
    use Concerns\HasIcon;
    use Concerns\HasSize;
    use Concerns\HasWeight;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.text-column';

    protected bool | Closure $canWrap = false;

    protected ?string $enum = null;

    protected bool | Closure $isBadge = false;

    protected bool | Closure $isBulleted = false;

    protected bool | Closure $isListWithLineBreaks = false;

    protected int | Closure | null $listLimit = null;

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function bulleted(bool | Closure $condition = true): static
    {
        $this->isBulleted = $condition;

        return $this;
    }

    public function listWithLineBreaks(bool | Closure $condition = true): static
    {
        $this->isListWithLineBreaks = $condition;

        return $this;
    }

    public function limitList(int | Closure | null $limit = 3): static
    {
        $this->listLimit = $limit;

        return $this;
    }

    /**
     * @param  string | array<scalar, scalar> | Arrayable  $enum
     */
    public function enum(string | array | Arrayable $enum, mixed $default = null): static
    {
        if (is_array($enum) || $enum instanceof Arrayable) {
            $this->formatStateUsing(static fn ($state): ?string => $enum[$state] ?? ($default ?? $state));

            return $this;
        }

        $this->enum = $enum;

        if (
            is_string($enum) &&
            function_exists('enum_exists') &&
            enum_exists($enum) &&
            is_a($enum, BackedEnum::class, allow_string: true) &&
            is_a($enum, LabelInterface::class, allow_string: true)
        ) {
            $this->formatStateUsing(static fn ($state): ?string => $enum::tryFrom($state)?->getLabel() ?? ($default ?? $state));
        }

        return $this;
    }

    public function rowIndex(bool $isFromZero = false): static
    {
        $this->getStateUsing(static function (HasTable $livewire, stdClass $rowLoop) use ($isFromZero): string {
            $rowIndex = $rowLoop->{$isFromZero ? 'index' : 'iteration'};

            return (string) ($rowIndex + ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1)));
        });

        return $this;
    }

    public function wrap(bool | Closure $condition = true): static
    {
        $this->canWrap = $condition;

        return $this;
    }

    public function canWrap(): bool
    {
        return (bool) $this->evaluate($this->canWrap);
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function isBulleted(): bool
    {
        return (bool) $this->evaluate($this->isBulleted);
    }

    public function isListWithLineBreaks(): bool
    {
        return $this->evaluate($this->isListWithLineBreaks) || $this->isBulleted();
    }

    public function getListLimit(): ?int
    {
        return $this->evaluate($this->listLimit);
    }
}
