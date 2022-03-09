<?php

namespace Filament\Resources\Concerns;

use Exception;

trait Translatable
{
    public static function getDefaultTranslatableLocale(): string
    {
        $firstLocaleKey = array_key_first(static::getTranslatableLocales());
        return static::getTranslatableLocales()[$firstLocaleKey];
    }

    public static function getTranslatableAttributes(): array
    {
        $model = static::getModel();

        if (! method_exists($model, 'getTranslatableAttributes')) {
            throw new Exception("Model [{$model}] must use trait [Spatie\Translatable\HasTranslations].");
        }

        $attributes = app($model)->getTranslatableAttributes();

        if (! count($attributes)) {
            throw new Exception("Model [{$model}] must have [\$translatable] properties defined.");
        }

        return $attributes;
    }

    public static function getTranslatableLocales(): array
    {
        return config('filament-spatie-laravel-translatable-plugin.default_locales');
    }
}
