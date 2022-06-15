<?php

namespace Filament\Forms\Components;

use App\Models\Plugin;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use function Filament\Forms\array_move_after;
use function Filament\Forms\array_move_before;
use Filament\Forms\ComponentContainer;
use Illuminate\Support\Str;

class Repeater extends Field
{
    use Concerns\CanBeCollapsed;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasContainerGridLayout;

    protected string $view = 'forms::components.repeater';

    protected string | Closure | null $createItemButtonLabel = null;

    protected bool | Closure $isItemCreationDisabled = false;

    protected bool | Closure $isItemDeletionDisabled = false;

    protected bool | Closure $isItemMovementDisabled = false;

    protected bool | Closure $isInset = false;

    protected ?Collection $cachedExistingRecords = null;

    protected string | Closure | null $orderColumn = null;

    protected string | Closure | null $relationship = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultItems(1);

        $this->afterStateHydrated(static function (Repeater $component, ?array $state): void {
            $items = collect($state ?? [])
                ->mapWithKeys(static fn ($itemData) => [(string) Str::uuid() => $itemData])
                ->toArray();

            $component->state($items);
        });

        $this->registerListeners([
            'repeater::createItem' => [
                function (Repeater $component, string $statePath): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $newUuid = (string) Str::uuid();

                    $livewire = $component->getLivewire();
                    data_set($livewire, "{$statePath}.{$newUuid}", []);

                    $component->getChildComponentContainers()[$newUuid]->fill();

                    $component->hydrateDefaultItemState($newUuid);

                    $component->collapsed(false, shouldMakeComponentCollapsible: false);
                },
            ],
            'repeater::deleteItem' => [
                function (Repeater $component, string $statePath, string $uuidToDelete): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = $component->getState();

                    unset($items[$uuidToDelete]);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveItemDown' => [
                function (Repeater $component, string $statePath, string $uuidToMoveDown): void {
                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_move_after($component->getState(), $uuidToMoveDown);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveItemUp' => [
                function (Repeater $component, string $statePath, string $uuidToMoveUp): void {
                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_move_before($component->getState(), $uuidToMoveUp);

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'repeater::moveItems' => [
                function (Repeater $component, string $statePath, array $uuids): void {
                    if ($component->isItemMovementDisabled()) {
                        return;
                    }

                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_merge(array_flip($uuids), $component->getState());

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
        ]);

        $this->createItemButtonLabel(static function (Repeater $component) {
            return __('forms::components.repeater.buttons.create_item.label', [
                'label' => lcfirst($component->getLabel()),
            ]);
        });

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            return array_values($state ?? []);
        });
    }

    public function createItemButtonLabel(string | Closure | null $label): static
    {
        $this->createItemButtonLabel = $label;

        return $this;
    }

    public function defaultItems(int | Closure $count): static
    {
        $this->default(static function (Repeater $component) use ($count): array {
            $items = [];

            $count = $component->evaluate($count);

            if (! $count) {
                return $items;
            }

            foreach (range(1, $count) as $index) {
                $items[] = [];
            }

            return $items;
        });

        return $this;
    }

    public function disableItemCreation(bool | Closure $condition = true): static
    {
        $this->isItemCreationDisabled = $condition;

        return $this;
    }

    public function disableItemDeletion(bool | Closure $condition = true): static
    {
        $this->isItemDeletionDisabled = $condition;

        return $this;
    }

    public function disableItemMovement(bool | Closure $condition = true): static
    {
        $this->isItemMovementDisabled = $condition;

        return $this;
    }

    public function inset(bool | Closure $condition = true): static
    {
        $this->isInset = $condition;

        return $this;
    }

    public function hydrateDefaultItemState(string $uuid): void
    {
        $this->getChildComponentContainers()[$uuid]->hydrateDefaultState();
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        $containers = collect($this->getState());

        if ($this->getRelationship()) {
            $records = $this->getCachedExistingRecords();

            $containers
                ->map(function ($itemData, $itemKey) use ($records): ComponentContainer {
                    return $this
                        ->getChildComponentContainer()
                        ->getClone()
                        ->statePath($itemKey)
                        ->model($records[$itemKey] ?? $this->getRelatedModel())
                        ->inlineLabel(false);
                });
        } else {
            $containers
                ->map(function ($itemData, $itemIndex): ComponentContainer {
                    return $this
                        ->getChildComponentContainer()
                        ->getClone()
                        ->statePath($itemIndex)
                        ->inlineLabel(false);
                });
        }

        return $containers->toArray();
    }

    public function getCreateItemButtonLabel(): string
    {
        return $this->evaluate($this->createItemButtonLabel);
    }

    public function isItemMovementDisabled(): bool
    {
        return $this->evaluate($this->isItemMovementDisabled) || $this->isDisabled();
    }

    public function isItemCreationDisabled(): bool
    {
        return $this->evaluate($this->isItemCreationDisabled) || $this->isDisabled() || (filled($this->getMaxItems()) && ($this->getMaxItems() <= $this->getItemsCount()));
    }

    public function isItemDeletionDisabled(): bool
    {
        return $this->evaluate($this->isItemDeletionDisabled) || $this->isDisabled();
    }

    public function isInset(): bool
    {
        return (bool) $this->evaluate($this->isInset);
    }

    public function orderable(string | Closure | null $column = 'sort'): static
    {
        $this->orderColumn = $column;
        $this->disableItemMovement(static fn (Repeater $component): bool => ! $component->evaluate($column));

        return $this;
    }

    public function relationship(string | Closure | null $name, ?Closure $callback = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->modifyRelationshipQueryUsing = $callback;

        return $this;
    }

    public function fillFromRelationship(): void
    {
        $this->state(
            $this->getStateFromRelatedRecords($this->getCachedExistingRecords()),
        );
    }

    protected function getStateFromRelatedRecords(Collection $records): array
    {
        if (! $records->count()) {
            return [];
        }

        $firstRecord = $records->first();

        if (
            ($activeLocale = $this->getLivewire()->getActiveFormLocale()) &&
            method_exists($firstRecord, 'getTranslatableAttributes')
        ) {
            $translatableAttributes = $firstRecord->getTranslatableAttributes();

            $records = $records->map(function (Model $record) use ($activeLocale, $translatableAttributes): array {
                $state = $record->toArray();

                if (method_exists($record, 'getTranslation')) {
                    foreach ($translatableAttributes as $attribute) {
                        $state[$attribute] = $record->getTranslation($attribute, $activeLocale);
                    }
                }

                return $state;
            });
        }

        return $records->toArray();
    }

    public function getLabel(): string
    {
        if ($this->label === null && $this->getRelationship()) {
            return (string) Str::of($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getOrderColumn(): ?string
    {
        return $this->evaluate($this->orderColumn);
    }

    public function getRelationship(): ?HasOneOrMany
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getModelInstance()->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getCachedExistingRecords(): Collection
    {
        if ($this->cachedExistingRecords) {
            return $this->cachedExistingRecords;
        }

        $relationship = $this->getRelationship();
        $relationshipQuery = $relationship->getQuery();

        if ($this->modifyRelationshipQueryUsing) {
            $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]);
        }

        if ($orderColumn = $this->getOrderColumn()) {
            $relationshipQuery->orderBy($orderColumn);
        }

        $localKeyName = $relationship->getLocalKeyName();

        return $this->cachedExistingRecords = $relationshipQuery->get()->mapWithKeys(
            fn (Model $item): array => ["record-{$item[$localKeyName]}" => $item],
        );
    }

    public function clearCachedExistingRecords(): void
    {
        $this->cachedExistingRecords = null;
    }

    protected function getRelatedModel(): string
    {
        return $this->getRelationship()->getModel()::class;
    }
}
