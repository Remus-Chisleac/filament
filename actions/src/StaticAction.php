<?php

namespace Filament\Actions;

use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Traits\Conditionable;

class StaticAction extends ViewComponent
{
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeLabeledFrom;
    use Concerns\CanBeOutlined;
    use Concerns\CanCallParentAction;
    use Concerns\CanClose;
    use Concerns\CanEmitEvent;
    use Concerns\CanOpenUrl;
    use Concerns\CanSubmitForm;
    use Concerns\HasAction;
    use Concerns\HasArguments;
    use Concerns\HasGroupedIcon;
    use Concerns\HasIndicator;
    use Concerns\HasKeyBindings;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasSize;
    use Concerns\HasTooltip;
    use Conditionable;
    use HasColor;
    use HasIcon;
    use HasExtraAttributes;

    public const BUTTON_VIEW = 'filament-actions::button-action';

    public const GROUPED_VIEW = 'filament-actions::grouped-action';

    public const ICON_BUTTON_VIEW = 'filament-actions::icon-button-action';

    public const LINK_VIEW = 'filament-actions::link-action';

    protected string $evaluationIdentifier = 'action';

    protected string $viewIdentifier = 'action';

    final public function __construct(?string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);
        $static->configure();

        return $static;
    }

    public function button(): static
    {
        $this->view(static::BUTTON_VIEW);

        return $this;
    }

    public function grouped(): static
    {
        $this->view(static::GROUPED_VIEW);

        return $this;
    }

    public function iconButton(): static
    {
        $this->view(static::ICON_BUTTON_VIEW);

        return $this;
    }

    public function link(): static
    {
        $this->view(static::LINK_VIEW);

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getLivewireClickHandler(): ?string
    {
        if (! $this->isLivewireClickHandlerEnabled()) {
            return null;
        }

        if (is_string($this->action)) {
            return $this->action;
        }

        if ($event = $this->getEvent()) {
            $arguments = collect([$event])
                ->merge($this->getEventData())
                ->when(
                    $this->getEmitToComponent(),
                    fn (Collection $collection, string $component) => $collection->prepend($component),
                )
                ->map(fn (mixed $value): string => Js::from($value)->toHtml())
                ->implode(', ');

            return match ($this->getEmitDirection()) {
                'self' => "\$emitSelf($arguments)",
                'to' => "\$emitTo($arguments)",
                'up' => "\$emitUp($arguments)",
                default => "\$emit($arguments)"
            };
        }

        if ($handler = $this->getParentActionCallLivewireClickHandler()) {
            $handler .= '(';
            $handler .= Js::from($this->getArguments());
            $handler .= ')';

            return $handler;
        }

        return null;
    }

    public function getAlpineClickHandler(): ?string
    {
        if (! $this->shouldClose()) {
            return null;
        }

        return 'close()';
    }

    /**
     * @deprecated Use `->extraAttributes()` instead.
     *
     * @param  array<mixed>  $attributes
     */
    public function withAttributes(array $attributes): static
    {
        return $this->extraAttributes($attributes);
    }
}
