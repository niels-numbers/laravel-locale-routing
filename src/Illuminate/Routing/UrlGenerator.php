<?php

namespace NielsNumbers\LocaleRouting\Illuminate\Routing;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class UrlGenerator extends BaseUrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        // Cache the current locale, so we can change it to automatically
        // resolve any translatable route parameters such as slugs.
        $appLocale = App::getLocale();
        $urlLocale = $parameters['locale'] ?? null;

        $resolvedName = $this->resolveLocalizedRouteName($name, $urlLocale, $appLocale);

        return parent::route($resolvedName, $parameters, $absolute);
    }

    protected function resolveLocalizedRouteName(string $name, string|null $urlLocale, string $appLocale): string
    {
        if (Route::has($name)) {
            return $name;
        }

        return $name;
    }


}
