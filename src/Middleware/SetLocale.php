<?php

namespace NielsNumbers\LocaleRouting\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use NielsNumbers\LocaleRouting\Contracts\DetectorInterface;
use NielsNumbers\LocaleRouting\Localizer;

class SetLocale
{
    public function __construct(protected Localizer $localizer) {}

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);

        if ($this->localizer->storesInSession()) {
            Session::put('locale', $locale);
        }

        if ($this->localizer->storesInCookie()) {
            Cookie::queue('locale', $locale, 60 * 24 * 30);
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }

    protected function detectLocale(Request $request): string
    {
        if ($locale = $request->route('locale')) {
            return $locale;
        }

        if ($this->localizer->storesInSession() && Session::has('locale')) {
            return Session::get('locale');
        }

        if ($this->localizer->storesInCookie() && $request->cookie('locale')) {
            return $request->cookie('locale');
        }

        if($locale = $this->detectLocaleFromDetecotrs($request)){
            return $locale;
        }
        return config('app.locale');
    }

    protected function detectLocaleFromDetecotrs(Request $request): ?string
    {
        foreach ($this->localizer->detectors() as $detectorClass) {
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
}
