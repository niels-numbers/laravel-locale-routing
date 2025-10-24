<?php

namespace NielsNumbers\LocaleRouting;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use NielsNumbers\LocaleRouting\Facades\Localizer as LocalizerFacade;
use NielsNumbers\LocaleRouting\Illuminate\Routing\UrlGenerator;
use NielsNumbers\LocaleRouting\Macros\LocalizeMacro;
use NielsNumbers\LocaleRouting\Services\UriTranslator;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/locale-routing.php' => config_path('locale-routing.php'),
        ]);

        $this->registerMacros();
    }

    public function register(): void
    {
        $this->app->singleton(Localizer::class, fn() => new Localizer(new UriTranslator()));
        $this->mergeConfigFrom(__DIR__ . '/../config/locale-routing.php', 'locale-routing');

        $this->registerUrlGenerator();
    }


    protected function registerUrlGenerator()
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            return new UrlGenerator(
                $routes,
                $app->rebinding(
                    'request', $this->requestRebinder()
                ),
                $app['config']['app.asset_url']
            );
        });

        $this->app->extend('url', function (UrlGeneratorContract $url, $app) {
            // Next we will set a few service resolvers on the URL generator so it can
            // get the information it needs to function. This just provides some of
            // the convenience features to this URL generator like "signed" URLs.
            $url->setSessionResolver(function () {
                return $this->app['session'] ?? null;
            });

            $url->setKeyResolver(function () {
                return $this->app->make('config')->get('app.key');
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }

    protected function registerMacros(): void
    {
        $macroRegisterName = LocalizerFacade::macroRegisterName();

        Route::macro($macroRegisterName, function (callable $callable) {
            App::make(RouteRegistrar::class)->register($callable);
        });
    }
}
