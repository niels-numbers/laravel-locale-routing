<?php

namespace NielsNumbers\LocaleRouting;

use Closure;
use Illuminate\Support\Facades\Route;

class RouteRegistrar
{
    public function register(Closure $closure): void
    {
        Route::group([
            'prefix' => '{locale}',
            'locale_type' => 'with_locale',
        ], $closure);

        Route::group([
            'as' => 'no_prefix.',
            'locale_type' => 'no_prefix',
        ], $closure);
    }
}