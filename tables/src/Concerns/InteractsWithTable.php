<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait InteractsWithTable
{
    use CanBeStriped;
    use CanPaginateRecords;
    use CanPollRecords;
    use CanReorderRecords;
    use CanSearchRecords;
    use CanSelectRecords;
    use CanSortRecords;
    use CanToggleColumns;
    use HasActions;
    use HasBulkActions;
    use HasColumns;
    use HasContent;
    use HasEmptyState;
    use HasFilters;
    use HasHeader;
    use HasRecords;
    use HasRecordAction;
    use HasRecordClasses;
    use HasRecordUrl;
    use Forms\Concerns\InteractsWithForms;

    protected Table $table;

    protected bool $shouldMountInteractsWithTable = false;

    public function bootedInteractsWithTable(): void
    {
        $this->table = Action::configureUsing(
            Closure::fromCallable([$this, 'configureTableAction']),
            fn (): Table => BulkAction::configureUsing(
                Closure::fromCallable([$this, 'configureTableBulkAction']),
                fn (): Table => $this->table($this->makeTable()),
            ),
        );

        $this->cacheForm('toggleTableColumnForm', $this->getTableColumnToggleForm());

        $this->cacheForm('tableFiltersForm', $this->getTableFiltersForm());

        $this->getTableColumnToggleForm()->fill(session()->get(
            $this->getTableColumnToggleFormStateSessionKey(),
            $this->getDefaultTableColumnToggleState()
        ));

        $filtersSessionKey = $this->getTableFiltersSessionKey();

        if ($this->getTable()->persistsFiltersInSession() && session()->has($filtersSessionKey)) {
            $this->tableFilters = array_merge(
                $this->tableFilters ?? [],
                session()->get($filtersSessionKey) ?? [],
            );
        }

        if (! count($this->tableFilters ?? [])) {
            $this->tableFilters = null;
        }

        $this->getTableFiltersForm()->fill($this->tableFilters);

        $searchSessionKey = $this->getTableSearchSessionKey();

        if ($this->getTable()->persistsSearchInSession() && session()->has($searchSessionKey)) {
            $this->tableSearch = session()->get($searchSessionKey) ?? '';
        }

        $columnSearchSessionKey = $this->getTableColumnSearchSessionKey();

        if ($this->getTable()->persistsColumnSearchInSession() && session()->has($columnSearchSessionKey)) {
            $this->tableColumnSearchQueries = session()->get($columnSearchSessionKey) ?? [];
        }

        if (! $this->shouldMountInteractsWithTable) {
            return;
        }

        if ($this->getTable()->isPaginated()) {
            $this->tableRecordsPerPage = $this->getDefaultTableRecordsPerPageSelectOption();
        }

        $this->tableSortColumn ??= $this->getTable()->getDefaultSortColumn();
        $this->tableSortDirection ??= $this->getTable()->getDefaultSortDirection();
    }

    public function mountInteractsWithTable(): void
    {
        $this->shouldMountInteractsWithTable = true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->actions($this->getTableActions())
            ->actionsColumnLabel($this->getTableActionsColumnLabel())
            ->actionsPosition($this->getTableActionsPosition())
            ->bulkActions($this->getTableBulkActions())
            ->columns($this->getTableColumns())
            ->columnToggleFormColumns($this->getTableColumnToggleFormColumns())
            ->columnToggleFormWidth($this->getTableColumnToggleFormWidth())
            ->content($this->getTableContent())
            ->contentFooter($this->getTableContentFooter())
            ->contentGrid($this->getTableContentGrid())
            ->defaultSort($this->getDefaultTableSortColumn(), $this->getDefaultTableSortDirection())
            ->description($this->getTableDescription())
            ->emptyState($this->getTableEmptyState())
            ->emptyStateActions($this->getTableEmptyStateActions())
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->filters($this->getTableFilters())
            ->filtersFormColumns($this->getTableFiltersFormColumns())
            ->filtersFormWidth($this->getTableFiltersFormWidth())
            ->filtersLayout($this->getTableFiltersLayout())
            ->header($this->getTableHeader())
            ->headerActions($this->getTableHeaderActions())
            ->modelLabel($this->getTableModelLabel())
            ->paginated($this->isTablePaginationEnabled())
            ->paginatedWhileReordering($this->isTablePaginationEnabledWhileReordering())
            ->paginationPageOptions($this->getTableRecordsPerPageSelectOptions())
            ->persistFiltersInSession($this->shouldPersistTableFiltersInSession())
            ->persistSearchInSession($this->shouldPersistTableSearchInSession())
            ->persistColumnSearchInSession($this->shouldPersistTableColumnSearchInSession())
            ->pluralModelLabel($this->getTablePluralModelLabel())
            ->poll($this->getTablePollingInterval())
            ->recordAction($this->getTableRecordActionUsing())
            ->recordClasses($this->getTableRecordClassesUsing())
            ->recordTitle(fn (Model $record): ?string => $this->getTableRecordTitle($record))
            ->recordUrl($this->getTableRecordUrlUsing())
            ->reorderable($this->getTableReorderColumn())
            ->selectCurrentPageOnly($this->shouldSelectCurrentPageOnly())
            ->striped($this->isTableStriped());
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return null;
    }

    public function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        if (filled($identifier = $this->getTable()->getQueryStringIdentifier())) {
            return $identifier . ucfirst($property);
        }

        return $property;
    }

    protected function getInteractsWithTableForms(): array
    {
        return [
            'mountedTableActionForm' => $this->getMountedTableActionForm(),
            'mountedTableBulkActionForm' => $this->getMountedTableBulkActionForm(),
        ];
    }

    public function getActiveTableLocale(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableQuery(): Builder | Relation | null
    {
        return null;
    }
}
