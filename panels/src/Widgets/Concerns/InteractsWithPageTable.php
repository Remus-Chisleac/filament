<?php

namespace Filament\Widgets\Concerns;

use Exception;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Livewire\Attributes\ReactiveProp;
use function Livewire\store;
use function Livewire\trigger;

trait InteractsWithPageTable
{
    /** @var array<string, int> */
    #[ReactiveProp]
    public $paginators = [];

    /**
     * @var array<string, string | array<string, string | null> | null>
     */
    #[ReactiveProp]
    public array $tableColumnSearches = [];

    #[ReactiveProp]
    public ?string $tableGrouping = null;

    #[ReactiveProp]
    public ?string $tableGroupingDirection = null;

    /**
     * @var array<string, mixed> | null
     */
    #[ReactiveProp]
    public ?array $tableFilters = null;

    #[ReactiveProp]
    public int | string | null $tableRecordsPerPage = null;

    /**
     * @var ?string
     */
    #[ReactiveProp]
    public $tableSearch = '';

    #[ReactiveProp]
    public ?string $tableSortColumn = null;

    #[ReactiveProp]
    public ?string $tableSortDirection = null;

    protected HasTable $tablePage;

    protected function getTablePage(): string
    {
        throw new Exception('You must define a `getTablePage()` method on your widget that returns the name of a Livewire component.');
    }

    protected function getTablePageInstance(): HasTable
    {
        if (isset($this->tablePage)) {
            return $this->tablePage;
        }

        /** @var HasTable $tableComponent */
        $page = app('livewire')->new($this->getPage());
        trigger('mount', $page, [], null, null);

        $page->paginators = $this->paginators;
        $page->tableColumnSearches = $this->tableColumnSearches;
        $page->tableFilters = $this->tableFilters;
        $page->tableGrouping = $this->tableGrouping;
        $page->tableGroupingDirection = $this->tableGroupingDirection;
        $page->tableRecordsPerPage = $this->tableRecordsPerPage;
        $page->tableSearch = $this->tableSearch;
        $page->tableSortColumn = $this->tableSortColumn;
        $page->tableSortDirection = $this->tableSortDirection;

        return $this->tablePage = $page;
    }

    protected function getPageTableQuery(): Builder
    {
        return $this->getTablePageInstance()->getFilteredSortedTableQuery();
    }

    protected function getPageTableRecords(): Collection | Paginator
    {
        return $this->getTablePageInstance()->getTableRecords();
    }
}
