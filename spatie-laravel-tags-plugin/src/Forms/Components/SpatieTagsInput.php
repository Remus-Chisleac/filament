<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\Tags\Tag;

class SpatieTagsInput extends TagsInput
{
    protected string | Closure | AllTagTypes | null $type;

    protected function setUp(): void
    {
        parent::setUp();

        //all all tag types by default:
        $this->type(new AllTagTypes());

        $this->loadStateFromRelationshipsUsing(static function (SpatieTagsInput $component, ?Model $record): void {
            if (! method_exists($record, 'tagsWithType')) {
                return;
            }

            $type = $component->getType();
            $record->load('tags');

            if($component->allowsAllTagTypes()) {
                $tags = $record->tags;
            }
            else {
                $tags = $record->tagsWithType($type);
            }

            $component->state($tags->pluck('name')->toArray());
        });

        $this->saveRelationshipsUsing(static function (SpatieTagsInput $component, ?Model $record, array $state) {
            if (! (method_exists($record, 'syncTagsWithType') && method_exists($record, 'syncTags'))) {
                return;
            }

            if ($type = $component->getType() && !$component->allowsAllTagTypes()) {
                $record->syncTagsWithType($state, $type);

                return;
            }

            $component->syncTagsWithAnyType($record, $state);
        });

        $this->dehydrated(false);
    }

    /**
     * Syncs tags with the record without taking types into account. This avoids recreating existing tags with an empty type.
     * The Spatie HasTags trait does not have functionality for this behaviour.
     * @param Model|null $record
     * @param array $state
     * @return void
     */
    private function syncTagsWithAnyType(?Model $record, array $state){
        $tagClassName = $record::getTagClassName();

        $tags = collect($state)->map(function ($tagName) use ($tagClassName) {
            $locale = $locale ?? $tagClassName::getLocale();

            $tag = $tagClassName::findFromStringOfAnyType($tagName, $locale);

            if (! $tag || $tag->isEmpty()) {
                $tag = $tagClassName::create([
                    'name' => [$locale => $tagName],
                ]);
            }

            return $tag;
        })->flatten();

        $record->tags()->sync($tags->pluck('id')->toArray());
    }

    public function type(string | Closure | AllTagTypes | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSuggestions(): array
    {
        if ($this->suggestions !== null) {
            return parent::getSuggestions();
        }

        $model = $this->getModel();
        $tagClass = $model ? $model::getTagClassName() : config('tags.tag_model', Tag::class);
        $type = $this->getType();
        $query = $tagClass::query();

        if(!$this->allowsAllTagTypes()){
            $query->when(
                filled($type),
                fn (Builder $query) => $query->where('type', $type),
                fn (Builder $query) => $query->where('type', null),
            );
        }

        return $query->pluck('name')
            ->toArray();
    }

    public function getType(): string | AllTagTypes | null
    {
        return $this->evaluate($this->type);
    }

    public function allowsAllTagTypes(): bool
    {
        return $this->getType() instanceof AllTagTypes;
    }
}
