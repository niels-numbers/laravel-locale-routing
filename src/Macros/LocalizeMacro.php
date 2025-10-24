<?php

namespace NielsNumbers\LocaleRouting\Macros;

use Closure;
use Illuminate\Support\Facades\Route;

class LocalizeMacro
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
