# Laravel Localization

This package is the maintained continuation of [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization).  
I (Adam Nielsen) was a collaborator on the original package, and since @mcamara has moved on, I am now maintaining it as version v3.

The purpose remains the same: detect the user’s preferred language from the request  
and redirect to the correct localized URL.

---

## What does this package do?

Example:

- User visits `example.com`
- Language is detected from request as German → redirect to `example.com/de`
- Otherwise fallback (e.g. English) → `example.com/en`

You can optionally **hide the default locale**.  
If `en` is default, the main URL stays `example.com` instead of `example.com/en`.

Behind the scenes, each route is registered twice:

- `/example-route`
- `/{locale}/example-route`

Once a language is stored in the session or cookie, requests without a locale are redirected to the stored language
— unless it’s the default locale with hidden prefix, in which case no redirect occurs.

URL translations are also supported.

> Note: This package does not make Eloquent models translatable.  
> For that, see [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable).
 
## Routing approach

The [original package](https://github.com/mcamara/laravel-localization) generated dynamic routes, which caused cache and compatibility issues.  
This maintained version introduces a new design: each route is registered **twice** —  
once with a `{locale}` placeholder, and once without (using a `no_prefix.` name).  
This is a clean alternative that supports route caching and avoids duplication per locale.

## Do I need this package?

Use this package if you want:

- automatic locale detection from the request (e.g. from the browser)
- automatic redirects to localized routes
- fully translatable routes (e.g. `/en/humans`, `/de/menschen`, etc.)

You **don’t** need it if you are fine with only:

- `example.com/de/blog`
- `example.com/en/blog`

and do not need `example.com/blog` or locale detection from the browser.

## Credits

- [@mcamara](https://github.com/mcamara) — original creator of [laravel-localization](https://github.com/mcamara/laravel-localization).
- [@codezero-be](https://github.com/codezero-be) — developed a static route-per-locale approach  
  (e.g. `en.index`, `de.index`, `es.index`). While this package follows a different routing strategy  
  (two routes per definition: one with `{locale}` and one without), many classes and much of the  
  implementation style are adapted from [laravel-localized-routes](https://github.com/codezero-be/laravel-localized-routes).

Since [@codezero-be](https://github.com/codezero-be) is no longer with us,  
I want to acknowledge his great work and influence on this package.  
Many of his ideas live on here, and I hope this helps to keep his contributions  
useful to the Laravel community for years to come.