<?php

namespace NielsNumbers\LocaleRouting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use NielsNumbers\LocaleRouting\Contracts\DetectorInterface;

class Localizer
{
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

    public function detectLocale(Request $request): ?string
    {
        foreach ($this->detectors() as $detectorClass) {
            $detector = app($detectorClass);
            if ($detector instanceof DetectorInterface) {
                $locale = $detector->detect($request);
                if ($locale) {
                    return $locale;
                }
            }
        }

        return null;
    }

    public function setLocale(string $locale): void
    {
        App::setLocale($locale);

        if ($this->storesInSession()) {
            Session::put('locale', $locale);
        }

        if ($this->storesInCookie()) {
            Cookie::queue('locale', $locale, 60 * 24 * 30);
        }
    }

    // @toDO will be part of the translatable urls..
    public function url(string $name, ?string $locale = null): string
    {
        // Placeholder for your translated URL logic
        return $name . ($locale ? " ({$locale})" : '');
    }

    public function macroRegisterName(): string
    {
        return 'localize';
    }
}
