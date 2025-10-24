<?php

namespace NielsNumbers\LocaleRouting\Tests\Feature\Services;

use Illuminate\Support\Facades\Lang;
use Orchestra\Testbench\TestCase;
use NielsNumbers\LocaleRouting\Services\UriTranslator;

class UriTranslatorTest extends TestCase
{
    protected UriTranslator $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translator = new UriTranslator();

        Lang::addLines([
            'routes.hello' => 'hallo',
            'routes.world' => 'wereld',
            'routes.override/hello/world' => 'iets/heel/anders',
            'routes.hello/world/{parameter}' => 'uri/met/{parameter}',
        ], 'nl');

    }

    /** @test */
    public function it_translates_full_uri_if_exact_match_exists()
    {
        $result = $this->translator->translate('override/hello/world', 'nl');
        $this->assertEquals('iets/heel/anders', $result);
    }

    /** @test */
    public function it_translates_individual_segments()
    {
        $result = $this->translator->translate('hello/world', 'nl');
        $this->assertEquals('hallo/wereld', $result);
    }

    /** @test */
    public function it_keeps_untranslated_segments()
    {
        $result = $this->translator->translate('hello/big/world', 'nl');
        $this->assertEquals('hallo/big/wereld', $result);
    }

    /** @test */
    public function it_preserves_placeholders()
    {
        $result = $this->translator->translate('hello/{parameter}', 'nl');
        $this->assertEquals('hallo/{parameter}', $result);
    }

    /** @test */
    public function it_translates_exact_match_with_placeholder()
    {
        $result = $this->translator->translate('hello/world/{parameter}', 'nl');
        $this->assertEquals('uri/met/{parameter}', $result);
    }
}
