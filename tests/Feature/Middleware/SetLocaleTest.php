<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase;
use NielsNumbers\LocaleRouting\Middleware\SetLocale;
use NielsNumbers\LocaleRouting\Localizer;
use NielsNumbers\LocaleRouting\ServiceProvider;

class SetLocaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Logs create permission conflicts in docker
        \Illuminate\Support\Facades\Log::swap(new \Illuminate\Log\Logger(
            new \Monolog\Logger('null', [new \Monolog\Handler\NullHandler()])
        ));
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        Config::set('app.locale', 'en');
        Config::set('locale-routing.hide_default_locale', true);
        Config::set('locale-routing.persist_locale.session', true);
        Config::set('locale-routing.persist_locale.cookie', true);
    }

    protected function defineRoutes($router)
    {
        $router->middleware(SetLocale::class)->group(function () use ($router) {
            $router->get('/{locale}/about', fn($locale) => $locale)->name('about.locale');
            $router->get('/about', fn() => response('about'))->name('about');
            $router->get('/{locale}', fn() => response('start'))->name('start.locale');
            $router->get('/', fn() => response('start'))->name('start');
        });
    }

    public function test_sets_locale_from_route_parameter()
    {
        $response = $this->get('/de/about');
        $this->assertEquals('de', App::getLocale());
        $response->assertOk(); // route doesn't exist, but middleware ran
    }

    public function test_stores_locale_in_session()
    {
        Config::set('locale-routing.use_session', true);
        Session::flush();

        $this->get('/de/about');
        $this->assertEquals('de', Session::get('locale'));
    }

    public function test_reads_locale_from_session()
    {
        Config::set('locale-routing.use_session', true);
        session(['locale' => 'de']);

        $this->get('/about');
        $this->assertEquals('de', Session::get('locale'));
    }
}
