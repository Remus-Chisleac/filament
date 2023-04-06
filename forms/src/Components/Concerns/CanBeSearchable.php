<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait CanBeSearchable
{
    protected bool | Closure $isSearchable = false;

    protected string | Htmlable | Closure | null $noSearchResultsMessage = null;

    protected int | Closure $searchDebounce = 1000;

    protected string | Closure | null $searchingMessage = null;

    protected string | Htmlable | Closure | null $searchPrompt = null;

    protected string | Closure | null $searchplaceholder = null;

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function noSearchResultsMessage(string | Htmlable | Closure | null $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function searchDebounce(int | Closure $debounce): static
    {
        $this->searchDebounce = $debounce;

        return $this;
    }

    public function searchingMessage(string | Closure | null $message): static
    {
        $this->searchingMessage = $message;

        return $this;
    }

    public function searchPrompt(string | Htmlable | Closure | null $message): static
    {
        $this->searchPrompt = $message;

        return $this;
    }

    public function searchPlaceholder(string | Closure | null $message): static
    {
        $this->searchPlaceholder = $message;

        return $this;
    }

    public function getNoSearchResultsMessage(): string | Htmlable
    {
        return $this->evaluate($this->noSearchResultsMessage) ?? __('forms::components.select.no_search_results_message');
    }

    public function getSearchPrompt(): string | Htmlable
    {
        return $this->evaluate($this->searchPrompt) ?? __('forms::components.select.search_prompt');
    }

    public function getSearchPlaceholder(): string | Htmlable
    {
        return $this->evaluate($this->searchPlaceholder) ?? __('forms::components.select.search_placeholder');
    }

    public function getSearchDebounce(): int
    {
        return $this->evaluate($this->searchDebounce);
    }

    public function getSearchingMessage(): string
    {
        return $this->evaluate($this->searchingMessage) ?? __('forms::components.select.searching_message');
    }

    public function isSearchable(): bool
    {
        return $this->evaluate($this->isSearchable);
    }
}
