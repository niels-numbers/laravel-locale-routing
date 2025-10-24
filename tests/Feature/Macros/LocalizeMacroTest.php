<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature\Macros;

use Illuminate\Support\Facades\Route;
use NielsNumbers\LocaleRouting\ServiceProvider;
use Orchestra\Testbench\TestCase;

class LocalizeMacroTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /** @test */
    public function it_registers_the_macro()
    {
        Route::localize(function () {
            Route::get('/test', fn () => 'ok')->name('test');
        });

        $routes = collect(Route::getRoutes())->map->getName();

        $this->assertTrue($routes->contains('with_locale.test'));
        $this->assertTrue($routes->contains('without_locale.test'));
        $this->assertCount(2, $routes);
    }
}
