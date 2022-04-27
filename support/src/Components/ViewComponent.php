<?php

namespace Filament\Support\Components;

use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component as BaseComponent;

abstract class ViewComponent extends BaseComponent implements Htmlable
{
    use Configurable;
    use Macroable;
    use Tappable;

    protected string $view;

    protected string $viewIdentifier;

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view($this->getView(), array_merge($this->data(), [
            $this->viewIdentifier => $this,
        ]));
    }
}
