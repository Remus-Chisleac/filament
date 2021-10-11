<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Table;

trait InteractsWithTable
{
    use HasColumns;

    protected Table $table;

    public function bootInteractsWithTable(): void
    {
        $this->table = $this->makeTable();
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
    }
}
