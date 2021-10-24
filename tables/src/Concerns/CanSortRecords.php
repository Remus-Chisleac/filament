<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    public $tableSortColumn = null;
    public $tableSortDirection = null;

    public function sortTable(?string $column = null): void
    {
        if ($column === $this->tableSortColumn) {
            if ($this->tableSortDirection === 'asc') {
                $direction = 'desc';
            } elseif ($this->tableSortDirection === 'desc') {
                $column = null;
                $direction = null;
            } else {
                $direction = 'asc';
            }
        } else {
            $direction = 'asc';
        }

        $this->tableSortColumn = $column;
        $this->tableSortDirection = $direction;

        $this->updatedTableSort();
    }

    public function updatedTableSort(): void
    {
        $this->selected = [];

        $this->resetPage();
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        $columnName = $this->tableSortColumn;

        if (! $columnName) {
            return $query;
        }

        $column = $this->getCachedTableColumn($columnName);

        if (! $column) {
            return $query;
        }

        $column->applySort($query, $this->tableSortDirection ?? 'asc');

        return $query;
    }
}
