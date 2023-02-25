<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasActions
{
    /**
     * @var array<string, Action | Closure>
     */
    protected array $actions = [];

    protected Model | string | null $actionFormModel = null;

    /**
     * @param  array<string, Action | Closure>  $actions
     */
    public function registerActions(array $actions): static
    {
        foreach ($actions as $actionName => $action) {
            if ($action instanceof Closure) {
                $this->actions[$actionName] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Form component actions must be an instance of ' . Action::class . ' or Closure.');
            }

            $this->actions[$action->getName()] = $action->component($this);
        }

        return $this;
    }

    public function getAction(string $name): Action | Closure | null
    {
        $action = $this->getActions()[$name] ?? null;

        if ($action instanceof Action) {
            $action->component($this);
        }

        return $action;
    }

    /**
     * @return array<string, Action | Closure>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function actionFormModel(Model | string | null $model): static
    {
        $this->actionFormModel = $model;

        return $this;
    }

    public function getActionFormModel(): Model | string | null
    {
        return $this->actionFormModel ?? $this->getRecord() ?? $this->getModel();
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->getActions());
    }
}
