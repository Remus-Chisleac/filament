<?php

namespace Filament\Forms\Components;

use Carbon\CarbonInterface;
use Closure;
use DateTime;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\View\ComponentAttributeBag;

class DateTimePicker extends Field
{
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.date-time-picker';

    protected string | Closure | null $displayFormat = null;

    protected array | Closure $extraTriggerAttributes = [];

    protected int | null $firstDayOfWeek = null;

    protected string | Closure | null $format = null;

    protected bool | Closure $isWithoutDate = false;

    protected bool | Closure $isWithoutSeconds = false;

    protected bool | Closure $isWithoutTime = false;

    protected DateTime | string | Closure | null $maxDate = null;

    protected DateTime | string | Closure | null $minDate = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (DateTimePicker $component, $state): void {
            if (! $state instanceof CarbonInterface) {
                return;
            }

            $state = $state->setTimezone( config('request.user.timezone') ?? config('app.timezone'))->format($component->getFormat());

            $component->state($state);
        });

        $this->mutateDehydratedStateUsing(static function (DateTimePicker $component, $state)  {
            if (blank($state)) {
                return $state;
            }

            try {
                return Carbon::createFromFormat(
                    $component->getFormat(),
                    $state, 
                    config('request.user.timezone') ?? config('app.timezone')
                )->setTimezone(config('app.timezone'))
                ->format($component->getFormat());
            }catch(\Throwable){
                return $state;
            }

        });

        $this->rule(
            'date',
            static fn (DateTimePicker $component): bool => $component->hasDate(),
        );
    }

    public function displayFormat(string | Closure | null $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    public function extraTriggerAttributes(array | Closure $attributes): static
    {
        $this->extraTriggerAttributes = $attributes;

        return $this;
    }

    public function firstDayOfWeek(int | null $day): static
    {
        if ($day < 0 || $day > 7) {
            $day = null;
        }

        $this->firstDayOfWeek = $day;

        return $this;
    }

    public function format(string | Closure | null $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function maxDate(DateTime | string | Closure | null $date): static
    {
        $this->maxDate = $date;

        $this->rule(static function (DateTimePicker $component) use ($date) {
            $date = $component->evaluate($date);

            if ($date instanceof DateTime) {
                $date = $date->format('Y-m-d');
            }

            return "before_or_equal:{$date}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->evaluate($date));

        return $this;
    }

    public function minDate(DateTime | string | Closure | null $date): static
    {
        $this->minDate = $date;

        $this->rule(static function (DateTimePicker $component) use ($date) {
            $date = $component->evaluate($date);

            if ($date instanceof DateTime) {
                $date = $date->format('Y-m-d');
            }

            return "after_or_equal:{$date}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->evaluate($date));

        return $this;
    }

    public function resetFirstDayOfWeek(): static
    {
        $this->firstDayOfWeek(null);

        return $this;
    }

    public function weekStartsOnMonday(): static
    {
        $this->firstDayOfWeek(1);

        return $this;
    }

    public function weekStartsOnSunday(): static
    {
        $this->firstDayOfWeek(7);

        return $this;
    }

    public function withoutDate(bool | Closure $condition = true): static
    {
        $this->isWithoutDate = $condition;

        return $this;
    }

    public function withoutSeconds(bool | Closure $condition = true): static
    {
        $this->isWithoutSeconds = $condition;

        return $this;
    }

    public function withoutTime(bool | Closure $condition = true): static
    {
        $this->isWithoutTime = $condition;

        return $this;
    }

    public function getDisplayFormat(): string
    {
        $format = $this->evaluate($this->displayFormat);

        if ($format) {
            return $format;
        }

        $format = $this->hasDate() ? 'M j, Y' : '';

        if (! $this->hasTime()) {
            return $format;
        }

        $format = $format ? "{$format} H:i" : 'H:i';

        if (! $this->hasSeconds()) {
            return $format;
        }

        return "{$format}:s";
    }

    public function getExtraTriggerAttributes(): array
    {
        return $this->evaluate($this->extraTriggerAttributes);
    }

    public function getExtraTriggerAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraTriggerAttributes());
    }

    public function getFirstDayOfWeek(): int
    {
        return $this->firstDayOfWeek ?? $this->getDefaultFirstDayOfWeek();
    }

    public function getFormat(): string
    {
        $format = $this->evaluate($this->format);

        if ($format) {
            return $format;
        }

        $format = $this->hasDate() ? 'Y-m-d' : '';

        if (! $this->hasTime()) {
            return $format;
        }

        $format = $format ? "{$format} H:i" : 'H:i';

        if (! $this->hasSeconds()) {
            return $format;
        }

        return "{$format}:s";
    }

    public function getMaxDate(): ?string
    {
        $date = $this->evaluate($this->maxDate);

        if ($date instanceof DateTime) {
            $date = $date->format($this->getFormat());
        }

        return $date;
    }

    public function getMinDate(): ?string
    {
        $date = $this->evaluate($this->minDate);

        if ($date instanceof DateTime) {
            $date = $date->format($this->getFormat());
        }

        return $date;
    }

    public function hasDate(): bool
    {
        return ! $this->isWithoutDate;
    }

    public function hasSeconds(): bool
    {
        return ! $this->isWithoutSeconds;
    }

    public function hasTime(): bool
    {
        return ! $this->isWithoutTime;
    }

    protected function getDefaultFirstDayOfWeek(): int
    {
        return config('forms.components.date_time_picker.first_day_of_week', 1);
    }
}
