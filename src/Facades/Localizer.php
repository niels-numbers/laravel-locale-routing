<?php

namespace NielsNumbers\LocaleRouting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hideDefaultLocale()
 * @method static bool storesInSession()
 * @method static bool storesInCookie()
 * @method static array detectors()
 * @method static ?string detectLocale(\Illuminate\Http\Request $request)
 * @method static void setLocale(string $locale)
 * @method static string url(string $name, ?string $locale = null)
 */
class Localizer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NielsNumbers\LocaleRouting\Localizer::class;
    }
}
