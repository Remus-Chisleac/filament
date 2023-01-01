<?php

namespace Filament\Actions;

use Illuminate\Database\Eloquent\Model;

class Action extends MountableAction implements Contracts\Groupable, Contracts\HasRecord, Contracts\SubmitsForm
{
    use Concerns\BelongsToLivewire;
    use Concerns\CanSubmitForm;
    use Concerns\HasMountableArguments;
    use Concerns\InteractsWithRecord;

    public function getLivewireCallActionName(): string
    {
        return 'callMountedAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if ($this->getUrl()) {
            return null;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', \'';
            $argumentsParameter .= str(json_encode($arguments))->replace('"', '\\"');
            $argumentsParameter .= '\'';
        }

        return "mountAction('{$this->getName()}'{$argumentsParameter})";
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'record' => $this->resolveEvaluationParameter(
                'record',
                fn (): ?Model => $this->getRecord(),
            ),
        ]);
    }
}
