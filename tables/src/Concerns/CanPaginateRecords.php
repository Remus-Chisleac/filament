<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

trait CanPaginateRecords
{
    use WithPagination {
        WithPagination::resetPage as livewireResetPage;
    }

    public $tableRecordsPerPage;

    protected int $defaultTableRecordsPerPageSelectOption = 0;

    public function updatedTableRecordsPerPage(): void
    {
        session()->put([
            $this->getTablePerPageSessionKey() => $this->getTableRecordsPerPage(),
        ]);

        $this->resetPage();
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        $perPage = $this->getTableRecordsPerPage();
        if ($perPage === -1) {
            $perPage = $query->count();
        }
        /** @var LengthAwarePaginator $records */
        $records = $query->paginate(
            $perPage,
            ['*'],
            $this->getTablePaginationPageName(),
        );

        return $records->onEachSide(1);
    }

    protected function getTableRecordsPerPage(): int
    {
        return intval($this->tableRecordsPerPage);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        $option = config('tables.pagination.records_per_page_select_options', [5, 10, 25, 50]);
        if ($this->isTablePaginationPerPageAllOptionEnabled()) {
            $option[] = -1;
        }

        return $option;
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        $perPage = session()->get(
            $this->getTablePerPageSessionKey(),
            $this->defaultTableRecordsPerPageSelectOption ?: config('tables.pagination.default_records_per_page'),
        );

        if (in_array($perPage, $this->getTableRecordsPerPageSelectOptions())) {
            return $perPage;
        }

        session()->remove($this->getTablePerPageSessionKey());

        return $this->getTableRecordsPerPageSelectOptions()[0];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    protected function isTablePaginationPerPageAllOptionEnabled(): bool
    {
        return true;
    }

    protected function getTablePaginationPageName(): string
    {
        return $this->getIdentifiedTableQueryStringPropertyNameFor('page');
    }

    public function getTablePerPageSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_per_page";
    }

    public function resetPage(?string $pageName = null): void
    {
        $this->livewireResetPage($pageName ?? $this->getTablePaginationPageName());
    }
}
