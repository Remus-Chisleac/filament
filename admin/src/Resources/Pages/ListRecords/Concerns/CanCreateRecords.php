<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Filament\Pages\Actions\Modal\Actions\ButtonAction as ModalButtonActions;

trait CanCreateRecords
{
    use UsesResourceForm;

    protected function hasCreateAction(): bool
    {
        return static::getResource()::canCreate();
    }

    protected function getCreateAction(): ButtonAction
    {
        return parent::getCreateAction()
            ->url(null)
            ->form($this->getCreateFormSchema())
            ->mountUsing(fn () => $this->fillCreateForm())
            ->modalActions($this->getCreateActionModalActions())
            ->modalHeading(__('filament::resources/pages/list-records.actions.create.modal.heading', ['label' => Str::title(static::getResource()::getLabel())]))
            ->action(fn () => $this->create());
    }

    protected function getCreateActionModalActions(): array
    {
        return [
            $this->getCreateModalAction(),
            $this->getCreateAndCreateAnotherModalAction(),
            $this->getCancelModalAction(),
        ];
    }

    protected function getCreateModalAction(): ModalButtonActions
    {
        return ModalButtonActions::make('create')
        ->label(__('filament::resources/pages/list-records.actions.create.modal.actions.create.label'))
        ->submit('callMountedAction')
        ->color('primary');
    }

    protected function getCreateAndCreateAnotherModalAction(): ModalButtonActions
    {
        return ModalButtonActions::make('createAndCreateAnother')
        ->label(__('filament::resources/pages/list-records.actions.create.modal.actions.create_and_create_another.label'))
        ->action('createAndCreateAnother')
        ->color('secondary');
    }

    protected function getCancelModalAction(): ModalButtonActions
    {
        return ModalButtonActions::make('cancel')
        ->label(__('tables::table.actions.modal.buttons.cancel.label'))
        ->cancel()
        ->color('secondary');
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    public function create(bool $another = false): void
    {
        $form = $this->getMountedActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $record = $this->handleRecordCreation($data);

        $form->model($record)->saveRelationships();

        $this->mountedTableActionRecord = $record->getKey();

        $this->callHook('afterCreate');

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $form->model($record::class);
            $this->mountedTableActionRecord = null;

            $form->fill();
        }
    }

    public function createAndCreateAnother(): void
    {
        $this->create(another: true);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
