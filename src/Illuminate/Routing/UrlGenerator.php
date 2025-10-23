<?php

namespace NielsNumbers\LocaleRouting\Illuminate\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use NielsNumbers\LocaleRouting\Facades\Localizer;

class UrlGenerator extends BaseUrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        $urlLocale = $parameters['locale'] ?? null;

        if (Route::has($name)) {
            return parent::route($name, $parameters, $absolute);
        }

        $defaultLocale = config('app.locale');
        $hideDefault = Localizer::hideDefaultLocale();

        // If locale is default and hide_default_locale is true → use route without locale prefix
        if ($hideDefault && $urlLocale === $defaultLocale) {
            $withoutLocaleName = 'without_locale.' . $name;
            if (Route::has($withoutLocaleName)) {
                // Need to unset locale here, otherwise it will
                // show as query paramter in url
                unset($parameters['locale']);
                return parent::route($withoutLocaleName, $parameters, $absolute);
            }
        }


        $resolvedName = 'with_locale.' .  $name;

        return parent::route($resolvedName, $parameters, $absolute);
    }

}
