<?php

namespace Filament\Tables;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Filter
{
    use Tappable;

    public $callback;

    public $context;

    public $hidden = false;

    public $label;

    public $name;

    public function __construct($name = null, $callback = null)
    {
        if ($name) {
            $this->name($name);
        }

        $this->callback($callback);

        $this->setUp();
    }

    protected function setUp()
    {
        //
    }

    public static function make($name = null, $callback = null)
    {
        return new static($name, $callback);
    }

    public function callback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function apply($query)
    {
        $callback = $this->callback;

        return $callback($query);
    }

    public function context($context)
    {
        $this->context = $context;

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! $this->context || in_array($this->context, $contexts)) return $this;

        $callback($this);

        return $this;
    }

    public function hidden()
    {
        $this->hidden = true;

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;

        $this->label(
            (string) Str::of($this->name)
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst(),
        );
    }

    public function only($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! in_array($this->context, $contexts)) return $this;

        $callback($this);

        return $this;
    }
}
