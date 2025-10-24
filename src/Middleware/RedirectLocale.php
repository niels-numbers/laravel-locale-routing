<?php

namespace NielsNumbers\LocaleRouting\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class RedirectLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Config::get('locale-routing.redirect_enabled', true)) {
            return $next($request);
        }

        $locale = App::getLocale();
        $default = Config::get('app.fallback_locale');
        $hideDefault = Config::get('locale-routing.hide_default_locale', true);

        $path = ltrim($request->path(), '/');
        // if route has locale prefix and it's default locale → redirect to non-prefixed
        if (preg_match("#^{$locale}/#", $path) && $locale === $default && $hideDefault) {
            $path = preg_replace("#^/?{$default}#", '', $request->path());
            return redirect(url($path));
        }

        // if route has no locale prefix and it's not default → redirect to prefixed
        if (! preg_match("#^[a-z]{2}/#", $path) && $locale !== $default) {
            return redirect(url("{$locale}/{$request->path()}"));
        }

        return $next($request);
    }
}
