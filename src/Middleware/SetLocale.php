<?php

namespace NielsNumbers\LocaleRouting\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use NielsNumbers\LocaleRouting\Localizer;

class SetLocale
{
    public function __construct(protected Localizer $localizer) {}

    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $default = Config::get('app.locale', 'en');
        $hideDefault = $this->localizer->hideDefaultLocale();

        $routeLocale = $request->route('locale');
        if ($routeLocale) {
            $this->localizer->setLocale($routeLocale);

            if ($routeLocale === $default && $hideDefault) {
                $path = preg_replace("#^/?{$default}#", '', $request->path());
                return redirect(url($path));
            }

            return $next($request);
        }

        $locale = null;
        if ($this->localizer->storesInSession()) {
            $locale = Session::get('locale');
        }
        if (!$locale && $this->localizer->storesInCookie()) {
            $locale = Cookie::get('locale');
        }

        $locale ??= $this->localizer->detectLocale($request);
        $locale ??= $default;

        $this->localizer->setLocale($locale);

        if ($locale !== $default || !$hideDefault) {
            return redirect(url("{$locale}/{$request->path()}"));
        }

        return $next($request);
    }
}
