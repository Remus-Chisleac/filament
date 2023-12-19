<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;

trait HasSelect
{
    use HasId;

    /**
     * @var array<string | array<string>> | Arrayable | string | Closure | null
     */
    protected array | Arrayable | string | Closure | null $options = null;

    protected ?string $placeholder = null;

    /**
     * @param  array<string | array<string>> | Arrayable | string | Closure | null  $options
     */
    public function options(array | Arrayable | string | Closure | null $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return array<string | array<string>>
     */
    public function getOptions(): array
    {
        $options = $this->evaluate($this->options) ?? [];

        $enum = $options;
        if (
            is_string($enum) &&
            enum_exists($enum)
        ) {
            if (is_a($enum, LabelInterface::class, allow_string: true)) {
                return collect($enum::cases())
                    ->mapWithKeys(fn ($case) => [
                        ($case?->value ?? $case->name) => $case->getLabel() ?? $case->name,
                    ])
                    ->all();
            }

            return collect($enum::cases())
                ->mapWithKeys(fn ($case) => [
                    ($case?->value ?? $case->name) => $case->name,
                ])
                ->all();
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }
}
