<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
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
            $router->get('/{locale}/about', fn() => response('about'))->name('about.locale');
            $router->get('/about', fn() => response('about'))->name('about');
            $router->get('/{locale}', fn() => response('start'))->name('start.locale');
            $router->get('/', fn() => response('start'))->name('start');
        });
    }

    /** @test */
    public function it_sets_locale_from_route()
    {
        $this->get('/de/about');
        $this->assertEquals('de', App::getLocale());
    }

    /** @test */
    public function it_redirects_if_default_locale_hidden()
    {
        $response = $this->get('/en/about');
        $response->assertRedirect('/about');
    }

    /** @test */
    public function it_redirects_if_default_locale_hidden_edge_case()
    {
        $response = $this->get('/en');
        $response->assertRedirect('/');
    }

    /** @test */
    public function it_redirects_to_prefixed_when_missing()
    {
        $this->withSession(['locale' => 'de']);
        $response = $this->get('/about');
        $response->assertRedirect('/de/about');
    }
}
