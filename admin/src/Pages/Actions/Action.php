<?php

namespace Filament\Pages\Actions;

use Closure;
use Filament\Facades\Filament;
use Filament\Pages\Actions\Modal\Actions\Action as ModalAction;
use Filament\Pages\Contracts\HasModel as HasModelContract;
use Filament\Pages\Contracts\HasRecord as HasRecordContract;
use Filament\Support\Actions\Action as BaseAction;
use Filament\Support\Actions\Concerns\CanBeDisabled;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\CanOpenUrl;
use Filament\Support\Actions\Concerns\CanSubmitForm;
use Filament\Support\Actions\Concerns\HasKeyBindings;
use Filament\Support\Actions\Concerns\HasTooltip;
use Filament\Support\Actions\Concerns\InteractsWithRecord;
use Filament\Support\Actions\Contracts\Groupable;
use Filament\Support\Actions\Contracts\HasRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use function Filament\Support\get_model_label;

class Action extends BaseAction implements Groupable, HasRecord
{
    use CanBeDisabled;
    use CanBeOutlined;
    use CanOpenUrl;
    use CanSubmitForm;
    use Concerns\BelongsToLivewire;
    use HasKeyBindings;
    use HasTooltip;
    use InteractsWithRecord {
        getRecord as getBaseRecord;
    }

    protected string $view = 'filament::pages.actions.button-action';

    public function getRecord(): ?Model
    {
        $record = $this->getBaseRecord();

        if ($record) {
            return $record;
        }

        $livewire = $this->getLivewire();

        if (! $livewire instanceof HasRecordContract) {
            return null;
        }

        return $livewire->getRecord();
    }

    public function getRecordTitle(?Model $record = null): string
    {
        $record ??= $this->getRecord();

        $title = $this->evaluate($this->recordTitle, ['record' => $record]);

        if (filled($title)) {
            return $title;
        }

        $livewire = $this->getLivewire();

        $title = null;

        if ($livewire instanceof HasRecordContract) {
            $title = $livewire->getRecordTitle();
        }

        if (filled($title)) {
            return $title;
        }

        if (! $record) {
            return $this->getModelLabel();
        }

        $titleAttribute = $this->getRecordTitleAttribute($record);

        if (filled($titleAttribute)) {
            return $record->getAttributeValue($titleAttribute);
        }

        return $record->getKey();
    }

    public function getModel(): ?string
    {
        $model = $this->evaluate($this->model);

        if (filled($model)) {
            return $model;
        }

        $livewire = $this->getLivewire();

        if ($livewire instanceof HasModelContract) {
            return $livewire->getModel();
        }

        $record = $this->getRecord();

        if (! $record) {
            return null;
        }

        return $record::class;
    }

    public function getModelLabel(): ?string
    {
        $label = $this->evaluate($this->modelLabel);

        if (filled($label)) {
            return $label;
        }

        $livewire = $this->getLivewire();

        if ($livewire instanceof HasModelContract) {
            return $livewire->getModelLabel();
        }

        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        return get_model_label($model);
    }

    public function getPluralModelLabel(): ?string
    {
        $label = $this->evaluate($this->pluralModelLabel);

        if (filled($label)) {
            return $label;
        }

        $livewire = $this->getLivewire();

        if ($livewire instanceof HasModelContract) {
            return $livewire->getPluralModelLabel();
        }

        $singularLabel = $this->getModelLabel();

        return filled($singularLabel) ? Str::plural($singularLabel) : null;
    }

    public function button(): static
    {
        $this->view('filament::pages.actions.button-action');

        return $this;
    }

    public function grouped(): static
    {
        $this->view('filament::pages.actions.grouped-action');

        return $this;
    }

    public function iconButton(): static
    {
        $this->view('filament::pages.actions.icon-button-action');

        return $this;
    }

    public function link(): static
    {
        $this->view('filament::pages.actions.link-action');

        return $this;
    }

    protected function getLivewireCallActionName(): string
    {
        return 'callMountedAction';
    }

    protected static function getModalActionClass(): string
    {
        return ModalAction::class;
    }

    public static function makeModalAction(string $name): ModalAction
    {
        /** @var ModalAction $action */
        $action = parent::makeModalAction($name);

        return $action;
    }

    public function notify(string | Closure | null $status, string | Closure | null $message): void
    {
        Filament::notify($status, $message);
    }

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
