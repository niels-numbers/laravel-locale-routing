<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature\Illuminate\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use NielsNumbers\LocaleRouting\ServiceProvider;
use NielsNumbers\LocaleRouting\Illuminate\Routing\UrlGenerator as CustomUrlGenerator;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class UrlGeneratorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /** @test */
    public function it_replaces_the_default_url_generator()
    {
        $url = $this->app->make(UrlGeneratorContract::class);

        $this->assertInstanceOf(CustomUrlGenerator::class, $url);
        $this->assertSame($url, app('url')); // both bindings are identical
    }

    /** @test */
    public function it_uses_custom_route_method()
    {
        Route::get('/test', fn () => 'ok')->name('test');

        /** @var \NielsNumbers\LocaleRouting\Illuminate\Routing\UrlGenerator $url */
        $url = app('url');

        $this->assertInstanceOf(CustomUrlGenerator::class, $url);

        // Because your route() method doesn’t return yet, we just check that it’s called
        $result = $url->route('test');
        $this->assertNull($result);
    }
}
