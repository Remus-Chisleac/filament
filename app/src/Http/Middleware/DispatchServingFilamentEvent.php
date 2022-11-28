<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Events\ServingFilament;
use Illuminate\Http\Request;

class DispatchServingFilamentEvent
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        ServingFilament::dispatch();

        return $next($request);
    }
}
