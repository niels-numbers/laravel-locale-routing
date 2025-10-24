<?php

namespace NielsNumbers\LocaleRouting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use NielsNumbers\LocaleRouting\Contracts\DetectorInterface;
use NielsNumbers\LocaleRouting\Services\UriTranslator;

class Localizer
{
    public function __construct(
        protected UriTranslator $translator
    ) {
    }

    public function supportedLocales(): bool
    {
        return Config::get('locale-routing.supported_locales', true);
    }


    public function hideDefaultLocale(): bool
    {
        return Config::get('locale-routing.hide_default_locale', true);
    }

    public function storesInSession(): bool
    {
        return Config::get('locale-routing.persist_locale.session', true);
    }

    public function storesInCookie(): bool
    {
        return Config::get('locale-routing.persist_locale.cookie', true);
    }

    public function detectors(): array
    {
        return Config::get('locale-routing.detectors', []);
    }

    public function url(string $uri, ?string $locale = null): string
    {
        return $this->translator->translate($uri, $locale);
    }

    public function macroRegisterName(): string
    {
        return 'localize';
    }
}
