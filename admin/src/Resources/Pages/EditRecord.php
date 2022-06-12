<?php

namespace Filament\Resources\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\ViewAction;
use Filament\Pages\Contracts\HasFormActions;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ComponentContainer $form
 */
class EditRecord extends Page implements HasFormActions
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $data;

    protected $queryString = [
        'activeRelationManager',
    ];

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/edit-record.breadcrumb');
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->getRecord()->toArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(bool $shouldRedirect = true): void
    {
        $this->callHook('beforeValidate');

        $data = $this->form->getState();

        $this->callHook('afterValidate');

        $data = $this->mutateFormDataBeforeSave($data);

        $this->callHook('beforeSave');

        $this->handleRecordUpdate($this->getRecord(), $data);

        $this->callHook('afterSave');

        $shouldRedirect = $shouldRedirect && ($redirectUrl = $this->getRedirectUrl());

        if (filled($this->getSavedNotificationMessage())) {
            $this->notify(
                'success',
                $this->getSavedNotificationMessage(),
            );
        }

        if ($shouldRedirect) {
            $this->redirect($redirectUrl);
        }
    }

    protected function getSavedNotificationMessage(): ?string
    {
        return __('filament::resources/pages/edit-record.messages.saved');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    public function openDeleteModal(): void
    {
        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'delete',
        ]);
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function delete(): void
    {
        abort_unless(static::getResource()::canDelete($this->getRecord()), 403);

        $this->callHook('beforeDelete');

        $this->getRecord()->delete();

        $this->callHook('afterDelete');

        if (filled($this->getDeletedNotificationMessage())) {
            $this->notify(
                'success',
                $this->getDeletedNotificationMessage(),
            );
        }

        $this->redirect($this->getDeleteRedirectUrl());
    }

    protected function getDeletedNotificationMessage(): ?string
    {
        return __('filament-support::actions/delete.single.messages.deleted');
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        return array_merge(
            (($resource::hasPage('view') && $resource::canView($this->getRecord())) ? [$this->getViewAction()] : []),
            ($resource::canDelete($this->getRecord()) ? [$this->getDeleteAction()] : []),
        );
    }

    protected function configureAction(Action $action): void
    {
        return match (true) {
            $action instanceof DeleteAction => $this->configureDeleteAction($action),
            $action instanceof ViewAction => $this->configureViewAction($action),
        };
    }

    protected function configureViewAction(ViewAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canView($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle());

        if ($resource::hasPage('view')) {
            $action->url(fn () => static::getResource()::getUrl('view', ['record' => $this->getRecord()]));
        } else {
            $action->form($this->getFormSchema());
        }
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getViewAction(): Action
    {
        return ViewAction::make();
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canDelete($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle())
            ->successRedirectUrl($resource::getUrl('index'));
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getDeleteAction(): Action
    {
        return DeleteAction::make()
            ->action(fn () => $this->delete());
    }

    protected function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament::resources/pages/edit-record.title', [
            'label' => $this->getRecordTitle(),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament::resources/pages/edit-record.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getSubmitFormAction(): Action
    {
        return $this->getSaveFormAction();
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament::resources/pages/edit-record.form.actions.cancel.label'))
            ->url(static::getResource()::getUrl())
            ->color('secondary');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->model($this->getRecord())
                ->schema($this->getFormSchema())
                ->statePath('data')
                ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
        ];
    }

    protected function getFormSchema(): array
    {
        return $this->getResourceForm(columns: config('filament.layout.forms.have_inline_labels') ? 1 : 2)->getSchema();
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    /**
     * @deprecated Use `->successRedirectUrl()` on the action instead.
     */
    protected function getDeleteRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->getRecord();
    }
}
