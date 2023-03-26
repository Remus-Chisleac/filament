<?php

namespace Filament\Tables\Columns;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Tables\Columns\Concerns\BelongsToLayout;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;
use stdClass;

class Column extends ViewComponent
{
    use BelongsToLayout;
    use Concerns\BelongsToTable;
    use Concerns\CanAggregateRelatedModels;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeSearchable;
    use Concerns\CanBeSortable;
    use Concerns\CanBeSummarized;
    use Concerns\CanBeToggled;
    use Concerns\CanCallAction;
    use Concerns\CanGrow;
    use Concerns\CanOpenUrl;
    use Concerns\CanSpanColumns;
    use Concerns\CanWrapHeader;
    use Concerns\HasAlignment;
    use Concerns\HasExtraHeaderAttributes;
    use Concerns\HasLabel;
    use Concerns\HasRowLoopObject;
    use Concerns\HasName;
    use Concerns\HasRecord;
    use Concerns\HasState;
    use Concerns\HasTooltip;
    use Concerns\InteractsWithTableQuery;
    use Conditionable;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'column';

    protected string $viewIdentifier = 'column';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => fn (): HasTable => $this->getLivewire(),
            'record' => fn (): ?Model => $this->getRecord(),
            'rowLoop' => fn (): ?stdClass => $this->getRowLoop(),
            'state' => fn (): mixed => $this->getState(),
            'table' => fn (): Table => $this->getTable(),
        ]);
    }
}
