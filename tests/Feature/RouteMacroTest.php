<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use NielsNumbers\LocaleRouting\ServiceProvider;

class RouteMacroTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /** @test */
    public function it_registers_the_macro()
    {
        Route::localized(function () {
            Route::get('/test', fn () => 'ok')->name('test');
        });

        $routes = collect(Route::getRoutes())->map->getName();

        $this->assertTrue($routes->contains('test'));
        $this->assertTrue($routes->contains('no_prefix.test'));
        $this->assertCount(2, $routes);
    }
}