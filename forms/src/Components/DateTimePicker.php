<?php

namespace Filament\Forms\Components;

use Carbon\Exceptions\InvalidFormatException;
use Closure;
use DateTime;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Support\Carbon;
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

    protected string | Closure | null $timezone = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(static function (DateTimePicker $component, $state): void {
            if (blank($state)) {
                return;
            }

            if (! $state instanceof DateTime) {
                try {
                    $state = Carbon::createFromFormat($component->getFormat(), $state);
                } catch (InvalidFormatException $exception) {
                    $state = Carbon::parse($state);
                }
            }

            $state->setTimezone($component->getTimezone());

            $component->state((string) $state);
        });

        $this->dehydrateStateUsing(static function (DateTimePicker $component, $state) {
            if (blank($state)) {
                return null;
            }

            if (! $state instanceof DateTime) {
                $state = Carbon::parse($state);
            }

            $state->shiftTimezone($component->getTimezone());
            $state->setTimezone(config('app.timezone'));

            return $state->format($component->getFormat());
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

        $this->rule(static function (DateTimePicker $component) {
            return "before_or_equal:{$component->getMaxDate()}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->getMaxDate());

        return $this;
    }

    public function minDate(DateTime | string | Closure | null $date): static
    {
        $this->minDate = $date;

        $this->rule(static function (DateTimePicker $component) {
            return "after_or_equal:{$component->getMinDate()}";
        }, static fn (DateTimePicker $component): bool => (bool) $component->getMinDate());

        return $this;
    }

    public function resetFirstDayOfWeek(): static
    {
        $this->firstDayOfWeek(null);

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

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

        if (! $this->hasTime()) {
            return config('forms.components.date_time_picker.display_formats.date');
        }

        if (! $this->hasDate()) {
            return $this->hasSeconds() ?
                config('forms.components.date_time_picker.display_formats.time_with_seconds') :
                config('forms.components.date_time_picker.display_formats.time');
        }

        return $this->hasSeconds() ?
            config('forms.components.date_time_picker.display_formats.date_time_with_seconds') :
            config('forms.components.date_time_picker.display_formats.date_time');
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
        return $this->evaluate($this->maxDate);
    }

    public function getMinDate(): ?string
    {
        return $this->evaluate($this->minDate);
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
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
