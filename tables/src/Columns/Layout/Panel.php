<?php

namespace Filament\Tables\Columns\Layout;

use Closure;

class Panel extends Component
{
    protected string $view = 'tables::columns.layout.panel';

    protected Closure | bool $collapsed = true;

    final public function __construct(array | Closure $schema)
    {
        $this->schema($schema);
    }

    public static function make(array | Closure $schema): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    public function collapsed(Closure | bool $collapsed = true)
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    public function isCollapsed()
    {
        return $this->evaluate($this->collapsed);
    }
}
