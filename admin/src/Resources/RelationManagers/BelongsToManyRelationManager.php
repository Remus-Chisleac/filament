<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class BelongsToManyRelationManager extends RelationManager
{
    use Concerns\CanAttachRecords;
    use Concerns\CanCreateRecords;
    use Concerns\CanDeleteRecords;
    use Concerns\CanDetachRecords;
    use Concerns\CanEditRecords;

    protected static string $view = 'filament::resources.relation-managers.belongs-to-many-relation-manager';

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $table = Table::make();

            $table->actions([
                $this->getEditTableAction(),
                $this->getDetachTableAction(),
                $this->getDeleteTableAction(),
            ]);

            $table->bulkActions(array_merge(
                ($this->canDeleteAny() ? [$this->getDeleteTableBulkAction()] : []),
                ($this->canDetachAny() ? [$this->getDetachTableBulkAction()] : []),
            ));

            $table->headerActions(array_merge(
                ($this->canCreate() ? [$this->getCreateButtonTableHeaderAction()] : []),
                ($this->canAttach() ? [$this->getAttachButtonTableHeaderAction()] : []),
            ));

            $this->resourceTable = static::table($table);
        }

        return $this->resourceTable;
    }

    protected function create(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $this->callHook('beforeCreate');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotColumns = $relationship->getPivotColumns();
        $pivotData = Arr::only($data, $pivotColumns);
        $data = Arr::except($data, $pivotColumns);

        $record = $relationship->getQuery()->create($data);
        $this->getMountedTableActionForm()->model($record)->saveRelationships();
        $relationship->attach($record, $pivotData);

        $this->callHook('afterCreate');
    }

    protected function save(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeEditValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterEditValidate');

        $this->callHook('beforeSave');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotColumns = $relationship->getPivotColumns();
        $pivotData = Arr::only($data, $pivotColumns);
        $data = Arr::except($data, $pivotColumns);

        $record = $this->getMountedTableActionRecord();
        $record->update($data);
        $relationship->updateExistingPivot($record, $pivotData);

        $this->callHook('afterSave');
    }

    // https://github.com/laravel/framework/issues/4962
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        /** @var Builder $query */
        $query->select(
            $relationship->getTable().'.*',
            $query->getModel()->getTable().'.*',
        );

        return $query;
    }
}
