<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Field extends Component
{
    protected $bindingAttribute = 'wire:model.defer';

    protected $defaultValue = null;

    protected $extraAttributes = [];

    protected $helpMessage;

    protected $hint;

    protected $isDisabled = false;

    protected $isRequired = false;

    protected $name;

    protected $rules = [];

    protected $validationAttribute;

    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function addRules($rules)
    {
        if (! is_array($rules)) {
            $rules = [$this->getName() => $rules];
        }

        foreach ($rules as $field => $conditionsToAdd) {
            if (is_numeric($field)) {
                $field = $this->getName();
            }

            if (! is_array($conditionsToAdd)) {
                $conditionsToAdd = explode('|', $conditionsToAdd);
            }

            $this->rules[$field] = collect($this->getRules($field) ?? [])
                ->filter(function ($originalCondition) use ($conditionsToAdd) {
                    if (! is_string($originalCondition)) {
                        return true;
                    }

                    $conditionsToAdd = collect($conditionsToAdd);

                    if ($conditionsToAdd->contains($originalCondition)) {
                        return false;
                    }

                    if (! Str::of($originalCondition)->contains(':')) {
                        return true;
                    }

                    $originalConditionType = (string) Str::of($originalCondition)->before(':');

                    return ! $conditionsToAdd->contains(function ($conditionToAdd) use ($originalConditionType) {
                        return $originalConditionType === (string) Str::of($conditionToAdd)->before(':');
                    });
                })
                ->push(...$conditionsToAdd)
                ->toArray();
        }

        return $this;
    }

    public function bindingAttribute($bindingAttribute)
    {
        $this->bindingAttribute = $bindingAttribute;

        return $this;
    }

    public function dependable()
    {
        $this->bindingAttribute('wire:model');

        return $this;
    }

    public function disabled()
    {
        $this->disabled = true;

        return $this;
    }

    public function enabled()
    {
        $this->disabled = false;

        return $this;
    }

    public function default($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function extraAttributes($attributes)
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function helpMessage($message)
    {
        $this->helpMessage = $message;

        return $this;
    }

    public function hint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    public function getBindingAttribute()
    {
        return $this->bindingAttribute;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getExtraAttributes()
    {
        return $this->extraAttributes;
    }

    public function getHelpMessage()
    {
        return $this->helpMessage;
    }

    public function getHint()
    {
        return $this->hint;
    }

    public function getId()
    {
        if ($this->id === null) {
            return (string) Str::of($this->getName())
                ->replace('.', '-')
                ->slug();
        }

        return parent::getId();
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRules($field = null)
    {
        if ($field !== null) {
            return $this->rules[$field] ?? null;
        }

        return $this->rules;
    }

    public function isDisabled()
    {
        return $this->isDisabled;
    }

    public function isRequired()
    {
        return $this->isRequired;
    }

    public function name($name)
    {
        $this->name = $name;

        $this->addRules([$this->getName() => ['nullable']]);
    }

    public function nullable()
    {
        $this->required = false;

        $this->removeRules([$this->getName() => ['required']]);
        $this->addRules([$this->getName() => ['nullable']]);

        return $this;
    }

    public function removeRules($rules)
    {
        if (! is_array($rules)) {
            $rules = [$this->getName() => $rules];
        }

        foreach ($rules as $field => $conditionsToRemove) {
            if (is_numeric($field)) {
                $field = $this->getName();
            }

            if (! is_array($conditionsToRemove)) $conditionsToRemove = explode('|', $conditionsToRemove);

            if (empty($conditionsToRemove)) {
                unset($this->rules[$field]);

                return;
            }

            $this->rules[$field] = collect($this->getRules($field) ?? [])
                ->filter(function ($originalCondition) use ($conditionsToRemove) {
                    if (! is_string($originalCondition)) {
                        return true;
                    }

                    $conditionsToRemove = collect($conditionsToRemove);

                    if ($conditionsToRemove->contains($originalCondition)) {
                        return false;
                    }

                    if (! Str::of($originalCondition)->contains(':')) {
                        return true;
                    }

                    $originalConditionType = (string) Str::of($originalCondition)->before(':');

                    return ! $conditionsToRemove->contains(function ($conditionToRemove) use ($originalConditionType) {
                        return $originalConditionType === (string) Str::of($conditionToRemove)->before(':');
                    });
                })
                ->toArray();
        }

        return $this;
    }

    public function rules($conditions)
    {
        $this->addRules([$this->getName() => $conditions]);

        return $this;
    }

    public function required()
    {
        $this->isRequired = true;

        $this->removeRules([$this->getName() => ['nullable']]);
        $this->addRules([$this->getName() => ['required']]);

        return $this;
    }

    public function requiredWith($field)
    {
        $this->isRequired = false;

        $this->removeRules([$this->getName() => ['nullable', 'required']]);
        $this->addRules([$this->getName() => ["required_with:$field"]]);

        return $this;
    }

    public function validationAttribute($attribute)
    {
        $this->validationAttribute = $attribute;

        return $this;
    }
}
