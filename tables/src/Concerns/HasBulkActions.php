<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\BulkAction;

/**
 * @property Form $mountedTableBulkActionForm
 */
trait HasBulkActions
{
    public $mountedTableBulkAction = null;

    public $mountedTableBulkActionData = [];

    protected array $cachedTableBulkActions;

    public function cacheTableBulkActions(): void
    {
        $actions = BulkAction::configureUsing(
            Closure::fromCallable([$this, 'configureTableBulkAction']),
            fn (): array => $this->getTableBulkActions(),
        );

        $this->cachedTableBulkActions = [];

        foreach ($actions as $action) {
            $action->table($this->getCachedTable());

            $this->cachedTableBulkActions[$action->getName()] = $action;
        }
    }

    protected function configureTableBulkAction(BulkAction $action): void
    {
    }

    public function callMountedTableBulkAction(?string $arguments = null)
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $action->arguments($arguments ? json_decode($arguments, associative: true) : []);

        $form = $this->getMountedTableBulkActionForm();

        $result = null;

        try {
            if ($this->mountedTableBulkActionHasForm()) {
                $action->callBeforeFormValidated();

                $action->formData($form->getState());

                $action->callAfterFormValidated();
            }

            $action->callBefore();

            $result = $action->call([
                'form' => $form,
            ]);

            $result = $action->callAfter() ?? $result;
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
        }

        $this->mountedTableBulkAction = null;
        $this->selectedTableRecords = [];

        $action->resetArguments();
        $action->resetFormData();

        $this->dispatchBrowserEvent('close-modal', [
            'id' => "{$this->id}-table-bulk-action",
        ]);

        return $result;
    }

    public function mountTableBulkAction(string $name, array $selectedRecords)
    {
        $this->mountedTableBulkAction = $name;
        $this->selectedTableRecords = $selectedRecords;

        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $this->cacheForm(
            'mountedTableBulkActionForm',
            fn () => $this->getMountedTableBulkActionForm(),
        );

        try {
            $hasForm = $this->mountedTableBulkActionHasForm();

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableBulkActionForm(),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return;
        } catch (Cancel $exception) {
            $this->mountedTableBulkAction = null;
            $this->selectedTableRecords = [];

            return;
        }

        if (! $this->mountedTableBulkActionShouldOpenModal()) {
            return $this->callMountedTableBulkAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => "{$this->id}-table-bulk-action",
        ]);
    }

    public function mountedTableBulkActionShouldOpenModal(): bool
    {
        $action = $this->getMountedTableBulkAction();

        return $action->isConfirmationRequired() ||
            $action->getModalContent() ||
            $this->mountedTableBulkActionHasForm();
    }

    public function mountedTableBulkActionHasForm(): bool
    {
        return (bool) count($this->getMountedTableBulkActionForm()?->getComponents() ?? []);
    }

    public function getCachedTableBulkActions(): array
    {
        return $this->cachedTableBulkActions;
    }

    public function getMountedTableBulkAction(): ?BulkAction
    {
        if (! $this->mountedTableBulkAction) {
            return null;
        }

        return $this->getCachedTableBulkAction($this->mountedTableBulkAction);
    }

    public function getMountedTableBulkActionForm(): ?Form
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableBulkActionForm')) {
            return $this->getCachedForm('mountedTableBulkActionForm');
        }

        return $action->getForm(
            $this->makeForm()
                ->model($this->getTableQuery()->getModel()::class)
                ->statePath('mountedTableBulkActionData')
                ->context($this->mountedTableBulkAction),
        );
    }

    public function getCachedTableBulkAction(string $name): ?BulkAction
    {
        $action = $this->getCachedTableBulkActions()[$name] ?? null;
        $action?->records($this->getSelectedTableRecords());

        return $action;
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
}
