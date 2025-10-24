<?php

namespace NielsNumbers\LocaleRouting\Macros;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use NielsNumbers\LocaleRouting\Facades\Localizer;
use NielsNumbers\LocaleRouting\Services\UriTranslator;

class TranslateMacro
{
    public function __construct(
        protected UriTranslator $translator
    ) {}

    /**
     * Register translated routes for all supported locales.
     *
     * For each supported locale:
     * - temporarily sets the app locale
     * - prefixes the route with the locale (e.g. /de/about)
     * - uses translated URIs
     * - creates a "without locale" route for the fallback locale
     */
    public function register(Closure $routes): void
    {
        $supported = Localizer::supportedLocales();
        $default   = Config::get('app.fallback_locale');
        $currentLocae = App::getLocale();
        $hide      = Localizer::hideDefaultLocale();

        foreach ($supported as $locale) {
            App::setLocale($locale);

            Route::prefix($locale)
                ->name("$locale.")
                ->group(function () use ($routes, $locale) {
                    // Inside, user calls Route::get(Localizer::uri('...'))
                    $routes();
                });

            // For default locale: create version without prefix
            if ($locale === $default && $hide) {
                Route::name('without_locale.')
                    ->group($routes);
            }
        }

        // Restore app locale
        App::setLocale($currentLocae);
    }
}
