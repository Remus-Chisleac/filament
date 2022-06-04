<?php

namespace Filament\Pages\Concerns;

use Filament\Forms;
use Filament\Pages\Actions\Action;
use Filament\Pages\Contracts;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Forms\ComponentContainer $mountedActionForm
 */
trait HasActions
{
    use Forms\Concerns\InteractsWithForms;

    public $mountedAction = null;

    public $mountedActionData = [];

    protected ?array $cachedActions = null;

    public function callMountedAction()
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        if ($action->hasForm()) {
            $action->callBeforeFormValidated();

            $action->formData($this->getMountedActionForm()->getState());

            $action->callAfterFormValidated();
        }

        $action->callBefore();

        $result = $action->call();

        try {
            return $action->callAfter() ?? $result;
        } finally {
            $this->mountedAction = null;
            $action->resetFormData();

            $this->dispatchBrowserEvent('close-modal', [
                'id' => 'page-action',
            ]);
        }
    }

    public function mountAction(string $name)
    {
        $this->mountedAction = $name;

        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        if ($action->isDisabled()) {
            return;
        }

        $this->cacheForm(
            'mountedActionForm',
            fn () => $this->getMountedActionForm(),
        );

        if ($action->hasForm()) {
            $action->callBeforeFormFilled();
        }

        app()->call($action->getMountUsing(), [
            'action' => $action,
            'form' => $this->getMountedActionForm(),
        ]);

        if ($action->hasForm()) {
            $action->callAfterFormFilled();
        }

        if (! $action->shouldOpenModal()) {
            return $this->callMountedAction();
        }

        $this->resetErrorBag();

        $this->dispatchBrowserEvent('open-modal', [
            'id' => 'page-action',
        ]);
    }

    protected function getCachedActions(): array
    {
        if ($this->cachedActions === null) {
            $this->cacheActions();
        }

        return $this->cachedActions;
    }

    protected function cacheActions(): void
    {
        $this->cachedActions = collect($this->getActions())
            ->mapWithKeys(function (Action $action): array {
                $action->livewire($this);

                return [$action->getName() => $action];
            })
            ->toArray();
    }

    public function getMountedAction(): ?Action
    {
        if (! $this->mountedAction) {
            return null;
        }

        $action = $this->getCachedAction($this->mountedAction);

        if ($action) {
            return $action;
        }

        if (! $this instanceof Contracts\HasFormActions) {
            return null;
        }

        return $this->getCachedFormAction($this->mountedAction);
    }

    protected function getHasActionsForms(): array
    {
        return [
            'mountedActionForm' => $this->getMountedActionForm(),
        ];
    }

    public function getMountedActionForm(): ?Forms\ComponentContainer
    {
        $action = $this->getMountedAction();

        if (! $action) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedActionForm')) {
            return $this->getCachedForm('mountedActionForm');
        }

        return $this->makeForm()
            ->schema($action->getFormSchema())
            ->statePath('mountedActionData')
            ->model($this->getMountedActionFormModel());
    }

    protected function getMountedActionFormModel(): Model | string | null
    {
        return null;
    }

    protected function getCachedAction(string $name): ?Action
    {
        return $this->getCachedActions()[$name] ?? null;
    }

    protected function getActions(): array
    {
        return [];
    }
}
