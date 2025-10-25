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
        $locale = $parameters['locale'] ?? App::getLocale();

        if (Route::has($name)) {
            return parent::route($name, $parameters, $absolute);
        }

        $defaultLocale = config('app.fallback_locale');
        $hideDefault = Localizer::hideDefaultLocale();

        // If locale is default and hide_default_locale is true → use route without locale prefix
        // This could be a localized or a translated route
        // @toDo I believe this might be a little more complex..
        // 1. if current locale App::getLocale is already set to default, then you can go ahead with below
        // but if App::getLocale is different, we need to use the url with_locale to assur emiddleware setLocale will trigger!!
        if ($hideDefault && $locale === $defaultLocale) {
            $withoutLocaleName = 'without_locale.' . $name;
            if (Route::has($withoutLocaleName)) {
                // Need to unset locale here, otherwise it will
                // show as query paramter in url
                unset($parameters['locale']);
                return parent::route($withoutLocaleName, $parameters, $absolute);
            }
        }


        // Check for localized route, locale is part of the url as parameter {localize}
        $resolvedName = 'with_locale.' .  $name;
        if (Route::has($resolvedName)) {
            $parameters['locale'] = $locale;
            return parent::route($resolvedName, $parameters, $absolute);
        }

        // Last hope, it might be a translated url for non default locale.
        // Locale is not a parameter, but written inside route attribute set on registration macro
        // Maybe I should add a fallback here. if its not existing, checl for fallback_locale?
        $resolvedName = "translated_{$locale}." .  $name;
        if (Route::has($resolvedName)) {
            return parent::route($resolvedName, $parameters, $absolute);
        }

        $resolvedName = "translated_{$defaultLocale}." .  $name;
        if($hideDefault){
            $resolvedName = 'without_locale.' . $name;
        }
        return parent::route($resolvedName, $parameters, $absolute);
    }

}
