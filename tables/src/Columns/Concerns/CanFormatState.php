<?php

namespace Filament\Tables\Columns\Concerns;

use Akaunting\Money;
use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    protected int | Closure | null $characterLimit = null;

    protected string | Closure | null $characterLimitEnd = null;

    protected int | Closure | null $wordLimit = null;

    protected string | Closure | null $wordLimitEnd = null;

    protected string | Closure | null $prefix = null;

    protected string | Closure | null $suffix = null;

    protected string | Closure | null $timezone = null;

    protected bool | Closure $isHtml = false;

    public function date(?string $format = null, ?string $timezone = null): static
    {
        $format ??= Table::$defaultDateDisplayFormat;

        $this->formatStateUsing(static function (Column $column, $state) use ($format, $timezone): ?string {
            /** @var TextColumn $column */
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $column->getTimezone())
                ->translatedFormat($format);
        });

        return $this;
    }

    public function dateTime(?string $format = null, ?string $timezone = null): static
    {
        $format ??= Table::$defaultDateTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function since(?string $timezone = null): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($timezone): ?string {
            /** @var TextColumn $column */
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $column->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function money(string | Closure | null $currency = null, bool $shouldConvert = true): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($currency, $shouldConvert): ?string {
            if (blank($state)) {
                return null;
            }

            if (blank($currency)) {
                $currency = env('DEFAULT_CURRENCY', 'USD');
            }

            return (new Money\Money(
                $state,
                (new Money\Currency(strtoupper($column->evaluate($currency)))),
                $shouldConvert,
            ))->format();
        });

        return $this;
    }

    public function numeric(int | Closure $decimalPlaces = 0, string | Closure | null $decimalSeparator = '.', string | Closure | null $thousandsSeparator = ','): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($decimalPlaces, $decimalSeparator, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            return number_format(
                $state,
                $column->evaluate($decimalPlaces),
                $column->evaluate($decimalSeparator),
                $column->evaluate($thousandsSeparator),
            );
        });

        return $this;
    }

    public function time(?string $format = null, ?string $timezone = null): static
    {
        $format ??= Table::$defaultTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function limit(int | Closure | null $length = 100, string | Closure | null $end = '...'): static
    {
        $this->characterLimit = $length;
        $this->characterLimitEnd = $end;

        return $this;
    }

    public function words(int $words = 100, string $end = '...'): static
    {
        $this->wordLimit = $words;
        $this->wordLimitEnd = $end;

        return $this;
    }

    public function prefix(string | Closure | null $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Closure | null $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function html(bool | Closure $condition = true): static
    {
        $this->isHtml = $condition;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function formatState(mixed $state): mixed
    {
        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);

        if ($characterLimit = $this->getCharacterLimit()) {
            $state = Str::limit($state, $characterLimit, $this->getCharacterLimitEnd());
        }

        if ($wordLimit = $this->getWordLimit()) {
            $state = Str::words($state, $wordLimit, $this->getWordLimitEnd());
        }

        if (filled($prefix = $this->getPrefix())) {
            $state = $prefix . $state;
        }

        if (filled($suffix = $this->getSuffix())) {
            $state = $state . $suffix;
        }

        if ($this->isHtml()) {
            return $state instanceof HtmlString ?
                $state :
                str($state)->sanitizeHtml()->toHtmlString();
        }

        return $state;
    }

    public function getCharacterLimit(): ?int
    {
        return $this->evaluate($this->characterLimit);
    }

    public function getCharacterLimitEnd(): ?string
    {
        return $this->evaluate($this->characterLimitEnd);
    }

    public function getWordLimit(): ?int
    {
        return $this->evaluate($this->wordLimit);
    }

    public function getWordLimitEnd(): ?string
    {
        return $this->evaluate($this->wordLimitEnd);
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }

    public function isHtml(): bool
    {
        return (bool) $this->evaluate($this->isHtml);
    }

    public function getPrefix(): ?string
    {
        return $this->evaluate($this->prefix);
    }

    public function getSuffix(): ?string
    {
        return $this->evaluate($this->suffix);
    }
}
