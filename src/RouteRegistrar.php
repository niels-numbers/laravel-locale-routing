<?php

namespace NielsNumbers\LocaleRouting;

use Closure;
use Illuminate\Support\Facades\Route;

class RouteRegistrar
{
    public function register(Closure $closure): void
    {
        Route::group([
            'as' => 'with_locale.',
            'prefix' => '{locale}',
            'locale_type' => 'with_locale',
        ], $closure);

        Route::group([
            'as' => 'without_locale.',
            'locale_type' => 'without_locale',
        ], $closure);
    }
}
