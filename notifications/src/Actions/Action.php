<?php

namespace Filament\Notifications\Actions;

use Closure;
use Filament\Actions\Contracts\Groupable;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class Action extends StaticAction implements Arrayable, Groupable
{
    protected string $viewIdentifier = 'action';

    protected bool | Closure $shouldMarkAsRead = false;

    protected bool | Closure $shouldMarkAsUnread = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::LINK_VIEW);

        $this->defaultSize('sm');
    }

    public function markAsRead(bool | Closure $condition = true): static
    {
        $this->shouldMarkAsRead = $condition;

        return $this;
    }

    public function markAsUnread(bool | Closure $condition = true): static
    {
        $this->shouldMarkAsUnread = $condition;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'color' => $this->getColor(),
            'event' => $this->getEvent(),
            'eventData' => $this->getEventData(),
            'dispatchDirection' => $this->getDispatchDirection(),
            'dispatchToComponent' => $this->getDispatchToComponent(),
            'extraAttributes' => $this->getExtraAttributes(),
            'icon' => $this->getIcon(),
            'iconPosition' => match ($iconPosition = $this->getIconPosition()) {
                IconPosition::After => 'after',
                IconPosition::Before => 'before',
                default => $iconPosition,
            },
            'iconSize' => match ($iconSize = $this->getIconSize()) {
                IconSize::Small => 'sm',
                IconSize::Medium => 'md',
                IconSize::Large => 'large',
                default => $iconSize,
            },
            'isOutlined' => $this->isOutlined(),
            'isDisabled' => $this->isDisabled(),
            'label' => $this->getLabel(),
            'shouldClose' => $this->shouldClose(),
            'shouldMarkAsRead' => $this->shouldMarkAsRead(),
            'shouldMarkAsUnread' => $this->shouldMarkAsUnread(),
            'shouldOpenUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'size' => $this->getSize(),
            'url' => $this->getUrl(),
            'view' => $this->getView(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $static = static::make($data['name']);

        $view = $data['view'] ?? null;

        if (filled($view) && ($static->getView() !== $view) && static::isViewSafe($view)) {
            $static->view($view);
        }

        if (filled($size = $data['size'] ?? null)) {
            $static->size($size);
        }

        $static->close($data['shouldClose'] ?? false);
        $static->color($data['color'] ?? null);
        $static->disabled($data['isDisabled'] ?? false);

        match ($data['dispatchDirection'] ?? null) {
            'self' => $static->dispatchSelf($data['event'] ?? null, $data['eventData'] ?? []),
            'to' => $static->dispatchTo($data['dispatchToComponent'] ?? null, $data['event'] ?? null, $data['eventData'] ?? []),
            default => $static->dispatch($data['event'] ?? null, $data['eventData'] ?? [])
        };

        $static->extraAttributes($data['extraAttributes'] ?? []);
        $static->icon($data['icon'] ?? null);
        $static->iconPosition($data['iconPosition'] ?? null);
        $static->iconSize($data['iconSize'] ?? null);
        $static->label($data['label'] ?? null);
        $static->markAsRead($data['shouldMarkAsRead'] ?? false);
        $static->markAsUnread($data['shouldMarkAsUnread'] ?? false);
        $static->outlined($data['isOutlined'] ?? false);
        $static->url($data['url'] ?? null, $data['shouldOpenUrlInNewTab'] ?? false);

        return $static;
    }

    public function getAlpineClickHandler(): ?string
    {
        if ($this->shouldMarkAsRead()) {
            return 'markAsRead()';
        }

        if ($this->shouldMarkAsUnread()) {
            return 'markAsUnread()';
        }

        return parent::getAlpineClickHandler();
    }

    /**
     * @param  view-string  $view
     */
    protected static function isViewSafe(string $view): bool
    {
        return Str::startsWith($view, 'filament-actions::');
    }

    public function shouldMarkAsRead(): bool
    {
        return (bool) $this->evaluate($this->shouldMarkAsRead);
    }

    public function shouldMarkAsUnread(): bool
    {
        return (bool) $this->evaluate($this->shouldMarkAsUnread);
    }
}
