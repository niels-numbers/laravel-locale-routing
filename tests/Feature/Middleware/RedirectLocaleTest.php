<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use NielsNumbers\LocaleRouting\Middleware\RedirectLocale;
use Orchestra\Testbench\TestCase;
use NielsNumbers\LocaleRouting\ServiceProvider;

class RedirectLocaleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('app.locale', 'en');

        Route::middleware(RedirectLocale::class)
            ->get('/{locale}/about', fn() => 'ok');
        Route::middleware(RedirectLocale::class)
            ->get('/about', fn() => 'ok');

        // Logs create permission conflicts in docker
        \Illuminate\Support\Facades\Log::swap(new \Illuminate\Log\Logger(
            new \Monolog\Logger('null', [new \Monolog\Handler\NullHandler()])
        ));
    }

    protected function defineEnvironment($app)
    {
        Config::set('app.fallback_locale', 'en');
    }

    public function test_redirects_default_locale_when_hidden()
    {
        App::setLocale('en');
        Config::set('locale-routing.hide_default_locale', true);

        $response = $this->get('/en/about');
        $response->assertRedirect('/about');
    }

    public function test_redirects_to_prefixed_locale_when_missing()
    {
        App::setLocale('de');
        Config::set('locale-routing.hide_default_locale', true);

        $response = $this->get('/about');
        $response->assertRedirect('/de/about');
    }

    public function test_no_redirect_when_disabled()
    {
        App::setLocale('en');
        Config::set('locale-routing.redirect_enabled', false);

        $response = $this->get('/about');
        $response->assertOk();
    }
}
