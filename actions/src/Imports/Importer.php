<?php

namespace Filament\Actions\Imports;

use Carbon\CarbonInterface;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Importer
{
    /** @var array<ImportColumn> */
    protected array $cachedColumns;

    /**
     * @var array<string, mixed>
     */
    protected array $originalData;

    /**
     * @var array<string, mixed>
     */
    protected array $data;

    protected ?Model $record;

    protected static ?string $model = null;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        readonly protected Import $import,
        readonly protected array $columnMap,
        readonly protected array $options,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data): void
    {
        $this->originalData = $this->data = $data;
        $this->record = null;

        $this->remapData();
        $this->sanitizeData();

        $this->record = $this->resolveRecord();

        if (! $this->record) {
            return;
        }

        $this->callHook('beforeValidate');
        $this->validateData();
        $this->callHook('afterValidate');

        $this->callHook('beforeFill');
        $this->fillRecord();
        $this->callHook('afterFill');

        $recordExists = $this->record->exists;

        $this->callHook('beforeSave');
        $this->callHook($recordExists ? 'beforeUpdate' : 'beforeCreate');
        $this->saveRecord();
        $this->callHook('afterSave');
        $this->callHook($recordExists ? 'afterUpdate' : 'afterCreate');

        $this->import->increment('successful_rows');
    }

    public function remapData(): void
    {
        $data = $this->data;

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();
            $rowColumnName = $this->columnMap[$columnName] ?? null;

            if (! array_key_exists($rowColumnName, $this->data)) {
                continue;
            }

            $data[$columnName] = $this->data[$rowColumnName];
        }

        $this->data = $data;
    }

    public function sanitizeData(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $this->data[$columnName] = $column->sanitizeState(
                $this->data[$columnName],
                $this->options,
            );
        }
    }

    public function resolveRecord(): ?Model
    {
        $keyName = app(static::getModel())->getKeyName();
        $keyColumnName = $this->columnMap[$keyName] ?? $keyName;

        return static::getModel()::find($this->data[$keyColumnName]);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(): void
    {
        $validator = Validator::make(
            $this->data,
            $this->getValidationRules(),
            $this->getValidationMessages(),
            $this->getValidationAttributes(),
        );

        $validator->validate();
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            $rules[$columnName] = $column->getDataValidationRules();

            if (
                $column->isArray() &&
                count($nestedRecursiveRules = $column->getNestedRecursiveDataValidationRules())
            ) {
                $rules["{$columnName}.*"] = $nestedRecursiveRules;
            }
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [];
    }

    public function fillRecord(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $state = $this->data[$columnName];

            if (blank($state) && $column->isBlankStateIgnored()) {
                continue;
            }

            $column->fillRecord($state);
        }
    }

    public function saveRecord(): void
    {
        $this->record->save();
    }

    /**
     * @return array<ImportColumn>
     */
    abstract public static function getColumns(): array;

    /**
     * @return array<Component>
     */
    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    /**
     * @return class-string<Model>
     */
    public static function getModel(): string
    {
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast('Importer')
            ->prepend('App\\Models\\');
    }

    abstract public static function getCompletedNotificationBody(Import $import): string;

    /**
     * @return array<int, object>
     */
    public function getJobMiddleware(): array
    {
        return [
            (new WithoutOverlapping("import{$this->import->id}"))->expireAfter(600),
        ];
    }

    public function getJobRetryUntil(): CarbonInterface
    {
        return now()->addDay();
    }

    /**
     * @return array<int, string>
     */
    public function getJobTags(): array
    {
        return ["import{$this->import->id}"];
    }

    /**
     * @return array<ImportColumn>
     */
    public function getCachedColumns(): array
    {
        return $this->cachedColumns ??= array_map(
            fn (ImportColumn $column) => $column->importer($this),
            static::getColumns(),
        );
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOriginalData(): array
    {
        return $this->originalData;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }
}
