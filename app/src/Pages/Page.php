<?php

namespace Filament\Pages;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Contracts\RendersFormComponentActionModal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Page extends Component implements HasActions, RendersFormComponentActionModal
{
    use Concerns\InteractsWithHeaderActions;
    use InteractsWithActions;

    protected static string $layout = 'filament::components.layouts.app';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = null;

    protected static ?string $title = null;

    protected static string $view;

    protected static string | array $middlewares = [];

    public static ?Closure $reportValidationErrorUsing = null;

    protected ?string $maxContentWidth = null;

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        Filament::registerNavigationItems(static::getNavigationItems());
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getRouteName(): string
    {
        $slug = static::getSlug();

        return "filament.pages.{$slug}";
    }

    public static function getRoutes(): Closure
    {
        return function () {
            $slug = static::getSlug();

            Route::get($slug, static::class)
                ->middleware(static::getMiddlewares())
                ->name($slug);
        };
    }

    public static function getMiddlewares(): string | array
    {
        return static::$middlewares;
    }

    public static function getSlug(): string
    {
        return static::$slug ?? str(static::$title ?? class_basename(static::class))
            ->kebab()
            ->slug();
    }

    public static function getUrl(array $parameters = [], bool $isAbsolute = true): string
    {
        return route(static::getRouteName(), $parameters, $isAbsolute);
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData())
            ->layout(static::$layout, $this->getLayoutData());
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getNavigationIcon(): string
    {
        return static::$navigationIcon ?? 'heroicon-o-document-text';
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    protected function getFooter(): ?View
    {
        return null;
    }

    protected function getHeader(): ?View
    {
        return null;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getHeading(): string
    {
        return $this->getTitle();
    }

    protected function getTitle(): string
    {
        return static::$title ?? (string) str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    protected function getMaxContentWidth(): ?string
    {
        return $this->maxContentWidth;
    }

    protected function getLayoutData(): array
    {
        return [
            'breadcrumbs' => $this->getBreadcrumbs(),
            'title' => $this->getTitle(),
            'maxContentWidth' => $this->getMaxContentWidth(),
        ];
    }

    protected function getViewData(): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    protected function onValidationError(ValidationException $exception): void
    {
        if (! static::$reportValidationErrorUsing) {
            return;
        }

        (static::$reportValidationErrorUsing)($exception);
    }

    protected function halt(): void
    {
        throw new Halt();
    }
}
