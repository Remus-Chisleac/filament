<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CheckboxList extends Field
{
    use Concerns\HasOptions;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.checkbox-list';

    protected string | Closure | null $relationshipTitleAttribute = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    protected bool | Closure $isBulkToggleable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (CheckboxList $component, $state) {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });
    }

    public function relationship(string | Closure $relationshipName, string | Closure $titleAttribute, ?Closure $callback = null): static
    {
        $this->relationshipTitleAttribute = $titleAttribute;
        $this->relationship = $relationshipName;

        $this->options(static function (CheckboxList $component) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query();

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipQuery->orderBy($component->getRelationshipTitleAttribute());
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$relationship->getRelatedKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getRelationshipTitleAttribute(), $relationship->getRelatedKeyName())
                ->toArray();
        });

        $this->loadStateFromRelationshipsUsing(static function (CheckboxList $component, ?array $state): void {
            $relationship = $component->getRelationship();

            /** @var Collection $relatedModels */
            $relatedModels = $relationship->getResults();

            $component->state(
                // Cast the related keys to a string, otherwise Livewire does not
                // know how to handle deselection.
                //
                // https://github.com/filamentphp/filament/issues/1111
                $relatedModels
                    ->pluck($relationship->getRelatedKeyName())
                    ->map(static fn ($key): string => strval($key))
                    ->toArray(),
            );
        });

        $this->saveRelationshipsUsing(static function (CheckboxList $component, ?array $state) {
            $component->getRelationship()->sync($state ?? []);
        });

        $this->dehydrated(false);

        return $this;
    }

    public function bulkToggleable(bool | Closure $condition = true): static
    {
        $this->isBulkToggleable = $condition;

        return $this;
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getOptionLabelFromRecord(Model $record): string
    {
        return $this->evaluate($this->getOptionLabelFromRecordUsing, ['record' => $record]);
    }

    public function getRelationshipTitleAttribute(): string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
    }

    public function getLabel(): string
    {
        if ($this->label === null && $this->getRelationship()) {
            return (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getRelationship(): ?BelongsToMany
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

    public function isBulkToggleable(): bool
    {
        return (bool) $this->evaluate($this->isBulkToggleable);
    }
}
