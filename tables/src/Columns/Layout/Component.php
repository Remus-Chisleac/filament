<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\BelongsToLayout;
use Filament\Tables\Columns\Concerns\BelongsToTable;
use Filament\Tables\Columns\Concerns\CanBeHidden;
use Filament\Tables\Columns\Concerns\CanGrow;
use Filament\Tables\Columns\Concerns\CanSpanColumns;
use Filament\Tables\Columns\Concerns\HasRecord;
use Filament\Tables\Columns\Concerns\HasRowLoopObject;
use Illuminate\Support\Traits\Conditionable;

class Component extends ViewComponent
{
    use BelongsToLayout;
    use BelongsToTable;
    use CanBeHidden;
    use CanSpanColumns;
    use CanGrow;
    use HasRecord;
    use HasRowLoopObject;
    use Conditionable;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'layout';

    protected string $viewIdentifier = 'layout';

    /**
     * @var array<Column | Component> | Closure
     */
    protected array | Closure $components = [];

    protected bool $isCollapsible = false;

    /**
     * @param array<Column | Component> | Closure $schema
     */
    public function schema(array | Closure $schema): static
    {
        $this->components($schema);

        return $this;
    }

    /**
     * @param array<Column | Component> | Closure $components
     */
    public function components(array | Closure $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function collapsible(bool $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    /**
     * @return array<string, Column>
     */
    public function getColumns(): array
    {
        $columns = [];

        foreach ($this->getComponents() as $component) {
            if ($component instanceof Column) {
                $columns[$component->getName()] = $component;

                continue;
            }

            $columns = array_merge($columns, $component->getColumns());
        }

        return $columns;
    }

    /**
     * @return array<Column | Component>
     */
    public function getComponents(): array
    {
        return array_map(function (Component | Column $component): Component | Column {
            return $component->layout($this);
        }, $this->evaluate($this->components));
    }

    public function isCollapsible(): bool
    {
        return $this->isCollapsible;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
            'rowLoop' => $this->getRowLoop(),
            'table' => $this->getTable(),
        ]);
    }
}
