<?php

namespace Filament\Support\Actions\Concerns;

use Filament\Tables\Actions\ForceDeleteAction;
use Illuminate\Database\Eloquent\Model;

trait CanForceDeleteRecords
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/force-delete.single.label'));

        $this->modalButton(__('filament-support::actions/force-delete.single.buttons.delete.label'));

        $this->successNotification(__('filament-support::actions/force-delete.single.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-s-trash');

        $this->requiresConfirmation();

        $this->action(static function (ForceDeleteAction $action, Model $record): void {
            $record->forceDelete();

            $action->sendSuccessNotification();
        });

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });
    }
}
