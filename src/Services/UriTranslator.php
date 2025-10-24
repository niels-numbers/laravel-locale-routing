<?php

namespace NielsNumbers\LocaleRouting\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

/**
 * Translate route URIs using language files in lang/{locale}/routes.php.
 *
 * This implementation is inspired by:
 * https://github.com/codezero-be/laravel-uri-translator/blob/master/src/UriTranslator.php
 *
 * Differences:
 * - Removed namespace/file resolution logic.
 */
class UriTranslator
{
    public function translate(string $uri, ?string $locale = null): string
    {
        $key = "routes.$uri";

        if (Lang::has($key, $locale)) {
            return Lang::get($key, [], $locale);
        }

        $segments = $this->splitSegments($uri);

        $translated = $segments->map(function ($segment) use ($locale) {
            // Keep placeholders as-is
            if (Str::startsWith($segment, '{')) {
                return $segment;
            }

            $key = "routes.$segment";

            return Lang::has($key, $locale)
                ? Lang::get($key, [], $locale)
                : $segment;
        });

        return $translated->implode('/');
    }

    protected function splitSegments(string $uri): Collection
    {
        return collect(explode('/', trim($uri, '/')));
    }
}
