<?php

namespace Filament\Resources\Resource\Concerns;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

trait HasNavigation
{
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationBadgeTooltip = null;

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationParentItem = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $activeNavigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    public static function registerNavigationItems(): void
    {
        if (filled(static::getCluster())) {
            return;
        }

        if (! static::shouldRegisterNavigation()) {
            return;
        }

        if (static::getParentResource()) {
            return;
        }

        if (! static::canAccess()) {
            return;
        }

        Filament::getCurrentPanel()
            ->navigationItems(static::getNavigationItems());
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        if (! static::hasPage('index')) {
            return [];
        }

        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteBaseName() . '.*'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return static::$subNavigationPosition;
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getNavigationParentItem(): ?string
    {
        return static::$navigationParentItem;
    }

    public static function navigationGroup(?string $group): void
    {
        static::$navigationGroup = $group;
    }

    public static function navigationParentItem(?string $item): void
    {
        static::$navigationParentItem = $item;
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon;
    }

    public static function navigationIcon(?string $icon): void
    {
        static::$navigationIcon = $icon;
    }

    public static function getActiveNavigationIcon(): string | Htmlable | null
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::getTitleCasePluralModelLabel();
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return static::$navigationBadgeTooltip;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public static function getNavigationBadgeColor(): string | array | null
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    public static function navigationLabel(?string $label): void
    {
        static::$navigationLabel = $label;
    }

    public static function navigationSort(?int $sort): void
    {
        static::$navigationSort = $sort;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    /**
     * @return array<NavigationItem | NavigationGroup>
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        return [];
    }
}