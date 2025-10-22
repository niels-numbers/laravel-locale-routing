<?php

namespace NielsNumbers\LocaleRouting\Illuminate\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use NielsNumbers\LocaleRouting\Config;

class UrlGenerator extends BaseUrlGenerator
{
    protected Config $config;

    public function __construct(RouteCollectionInterface $routes, Request $request, string|null $assetUrl, Config $config = null)
    {
        parent::__construct($routes, $request, $assetUrl);

        $this->config = $config;
    }

    public function route($name, $parameters = [], $absolute = true)
    {
        $urlLocale = $parameters['locale'] ?? null;

        if (Route::has($name)) {
            return parent::route($name, $parameters, $absolute);
        }

        $defaultLocale = config('app.locale');
        $hideDefault = $this->config->hideDefaultLocale();

        // If locale is default and hide_default_locale is true → use route without locale prefix
        if ($hideDefault && $urlLocale === $defaultLocale) {
            $withoutLocaleName = 'without_locale.' . $name;
            if (Route::has($withoutLocaleName)) {
                unset($parameters['locale']);
                return parent::route($withoutLocaleName, $parameters, $absolute);
            }
        }


        $resolvedName = 'with_locale.' .  $name;

        return parent::route($resolvedName, $parameters, $absolute);
    }

}
