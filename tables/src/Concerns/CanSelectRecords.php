<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait CanSelectRecords
{
    public array $selectedTableRecords = [];

    public function deselectAllTableRecords(): void
    {
        $this->selectedTableRecords = [];
    }

    public function toggleSelectAllTableRecords(): void
    {
        $query = $this->getFilteredTableQuery();

        $allRecords = $query->pluck($query->getModel()->getKeyName());

        if ($allRecords->count() > $this->getSelectedTableRecordCount()) {
            $this->selectedTableRecords = $allRecords->toArray();

            return;
        }

        $this->deselectAllTableRecords();
    }

    public function toggleSelectTableRecordsOnPage(): void
    {
        $pageRecords = $this->getTableRecords()
            ->pluck($this->getTableQuery()->getModel()->getKeyName())
            ->toArray();

        if (array_diff($pageRecords, $this->selectedTableRecords)) {
            $this->selectTableRecords($pageRecords);

            return;
        }

        $this->deselectTableRecords($pageRecords);
    }

    public function toggleSelectTableRecord(string $record): void
    {
        if (! $this->isTableRecordSelected($record)) {
            $this->selectTableRecord($record);

            return;
        }

        $this->deselectTableRecord($record);
    }

    public function areAllTableRecordsOnCurrentPageSelected(): bool
    {
        if (! $this->isTablePaginationEnabled()) {
            $allRecordsCount = $this->getAllTableRecordsCount();
            $selectedRecordsCount = $this->getSelectedTableRecordCount();

            return $allRecordsCount && ($allRecordsCount === $selectedRecordsCount);
        }

        $pageRecords = $this->getTableRecords()
            ->pluck($this->getTableQuery()->getModel()->getKeyName())
            ->toArray();

        return (bool) ! array_diff($pageRecords, $this->selectedTableRecords);
    }

    public function areAllTableRecordsSelected(): bool
    {
        $allRecordsCount = $this->getAllTableRecordsCount();
        $selectedRecordCount = $this->getSelectedTableRecordCount();

        return (bool) $selectedRecordCount && ($allRecordsCount === $selectedRecordCount);
    }

    public function getAllTableRecordsCount(): int
    {
        return $this->getFilteredTableQuery()->count();
    }

    public function getSelectedTableRecords(): Collection
    {
        return $this->getTableQuery()->find($this->selectedTableRecords);
    }

    public function getSelectedTableRecordCount(): int
    {
        return count($this->selectedTableRecords);
    }

    public function isTableRecordSelected(string $record): bool
    {
        return in_array($record, $this->selectedTableRecords);
    }

    public function isTableSelectionEnabled(): bool
    {
        return (bool) count($this->getCachedTableBulkActions());
    }

    protected function deselectTableRecord(string $record): void
    {
        $key = array_search($record, $this->selectedTableRecords);

        unset($this->selectedTableRecords[$key]);
    }

    protected function deselectTableRecords(array $records): void
    {
        foreach ($records as $record) {
            if (! $this->isTableRecordSelected($record)) {
                continue;
            }

            $this->deselectTableRecord($record);
        }
    }

    protected function selectTableRecord(string $record): void
    {
        $this->selectedTableRecords[] = $record;
    }

    protected function selectTableRecords(array $records): void
    {
        foreach ($records as $record) {
            if ($this->isTableRecordSelected($record)) {
                continue;
            }

            $this->selectTableRecord($record);
        }
    }

    protected function areSelectIndicatorActionsEnabled(): bool
    {
        return true;
    }

    protected function areSelectBulkActionsEnabled(): bool
    {
        return true;
    }
}
