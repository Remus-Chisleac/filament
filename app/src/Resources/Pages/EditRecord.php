<?php

namespace Filament\Resources\Pages;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Pages\Concerns\HasFormActions;
use Filament\Pages\Contracts\HasCachedFormActions;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Form $form
 */
class EditRecord extends Page implements HasCachedFormActions
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use HasFormActions;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $data;

    public ?string $previousUrl = null;

    protected $queryString = [
        'activeRelationManager',
    ];

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/edit-record.breadcrumb');
    }

    public function getFormTabLabel(): ?string
    {
        return __('filament::resources/pages/edit-record.form.tab.label');
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->getRecord()->attributesToArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function refreshFormData(array $attributes): void
    {
        $this->data = array_merge(
            $this->data,
            $this->getRecord()->only($attributes),
        );
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(bool $shouldRedirect = true): void
    {
        $this->authorizeAccess();

        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getRecord(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            return;
        }

        $this->getSavedNotification()?->send();

        if ($shouldRedirect && ($redirectUrl = $this->getRedirectUrl())) {
            $this->redirect($redirectUrl);
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle());
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return $this->getSavedNotificationMessage() ?? __('filament::resources/pages/edit-record.messages.saved');
    }

    /**
     * @deprecated Use `getSavedNotificationTitle()` instead.
     */
    protected function getSavedNotificationMessage(): ?string
    {
        return null;
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

    protected function configureAction(Action $action): void
    {
        match (true) {
            $action instanceof DeleteAction => $this->configureDeleteAction($action),
            $action instanceof ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof RestoreAction => $this->configureRestoreAction($action),
            $action instanceof ViewAction => $this->configureViewAction($action),
            default => null,
        };
    }

    protected function configureViewAction(ViewAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canView($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle())
            ->form(fn (Form $form): Form => static::getResource()::form($form));

        if ($resource::hasPage('view')) {
            $action->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->getRecord()]));
        }
    }

    protected function configureForceDeleteAction(ForceDeleteAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canForceDelete($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle())
            ->successRedirectUrl($resource::getUrl('index'));
    }

    protected function configureReplicateAction(ReplicateAction $action): void
    {
        $action
            ->authorize(static::getResource()::canReplicate($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle());
    }

    protected function configureRestoreAction(RestoreAction $action): void
    {
        $action
            ->authorize(static::getResource()::canRestore($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle());
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
            ->url($this->previousUrl ?? static::getResource()::getUrl())
            ->color('secondary');
    }

    public function form(Form $form): Form
    {
        return static::getResource()::form(
            $form
                ->context('edit')
                ->model($this->getRecord())
                ->statePath('data')
                ->columns(config('filament.layout.forms.have_inline_labels') ? 1 : 2)
                ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
        );
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->getRecord();
    }
}
