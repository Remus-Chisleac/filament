<?php

namespace Filament\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Form $form
 */
class ViewRecord extends Page
{
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use InteractsWithFormActions;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::resources.pages.view-record';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    /**
     * @var array<int | string, string | array<mixed>>
     */
    protected $queryString = [
        'activeRelationManager',
    ];

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/view-record.breadcrumb');
    }

    public function getFormTabLabel(): ?string
    {
        return __('filament::resources/pages/view-record.form.tab.label');
    }

    public function mount(int | string $record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->getRecord()->attributesToArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param  array<string>  $attributes
     */
    protected function refreshFormData(array $attributes): void
    {
        $this->data = array_merge(
            $this->data,
            $this->getRecord()->only($attributes),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function configureAction(Action $action): void
    {
        match (true) {
            $action instanceof DeleteAction => $this->configureDeleteAction($action),
            $action instanceof EditAction => $this->configureEditAction($action),
            $action instanceof ForceDeleteAction => $this->configureForceDeleteAction($action),
            $action instanceof ReplicateAction => $this->configureReplicateAction($action),
            $action instanceof RestoreAction => $this->configureRestoreAction($action),
            default => null,
        };
    }

    protected function configureEditAction(EditAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canEdit($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle())
            ->form(fn (Form $form): Form => static::getResource()::form($form));

        if ($resource::hasPage('edit')) {
            $action->url(fn (): string => static::getResource()::getUrl('edit', ['record' => $this->getRecord()]));
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

    public function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament::resources/pages/view-record.title', [
            'label' => $this->getRecordTitle(),
        ]);
    }

    public function form(Form $form): Form
    {
        return static::getResource()::form(
            $form
                ->operation('view')
                ->disabled()
                ->model($this->getRecord())
                ->statePath('data')
                ->columns($this->hasInlineFormLabels() ? 1 : 2)
                ->inlineLabel($this->hasInlineFormLabels()),
        );
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->getRecord();
    }
}
