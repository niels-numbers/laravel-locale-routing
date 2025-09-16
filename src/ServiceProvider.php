<?php

namespace NielsNumbers\LocaleRouting;

use CodeZero\LocalizedRoutes\LocalizedRoutesRegistrar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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
        $this->app->singleton(Config::class, function ($app) {
            return new Config($app['config']->get('locale-routing', []));
        });
    }

    protected function registerMacros(): void
    {
        /** @var Config $config */
        $config = App::make(Config::class);
        $macroRegisterName = $config->macroRegisterName();

        Route::macro($macroRegisterName, function (callable $callable) {
            App::make(RouteRegistrar::class)->register($callable);
        });
    }
}