# Laravel Locale Routing

Detect the user’s preferred language from the request and redirect them to the correct localized URL.  
You can hide the default locale in the URI or translate your URIs.

---

## Example how this works

- User visits `example.com`
- Language is detected as German → redirect to `example.com/de`
- Otherwise fallback to English → `example.com/en`

You can choose to hide your default locale. For example, if `en` is the default locale,  the main URL stays `example.com` instead of `example.com/en`.

On the first visit to `example.com` (without a locale), the package will try to detect the language from the request.  
The result is stored in the session or a cookie (configurable) and used for all future requests.

Behind the scenes, each route is registered twice: once with a `{locale}` placeholder, and once without.  
You don’t need to worry about this — just keep using the `route(...)` helper as usual - we do the mapping to the correct route behind the scenes.
This is compatible with `ziggy` out of the box.
The package takes care of the rest, enabling automatic redirects, URL translation,  
and full compatibility with Laravel’s route cache.

URL translations are also supported.

## Do I need this package?

Use this package if you want:

- automatic locale detection from the request (e.g. from the browser)
- automatic redirects to localized routes
- fully translatable routes (e.g. `/en/humans`, `/de/menschen`, etc.)

You **don’t** need it if you are fine with only:

- `example.com/de/blog`
- `example.com/en/blog`

and do not need `example.com/blog` or locale detection from the browser.

## Comparison to Other Packages

- **[mcamara/laravel-localization](https://github.com/mcamara/laravel-localization) (deprecated)**  
  This package is the modern successor to *laravel-localization*, which is no longer maintained.
  The original package was the first package that tried to solve the routing problem.
It generated routes dynamically at runtime, making it incompatible with `php artisan route:cache` and several Laravel packages.
  In contrast, this package registers **two static routes** per definition — one with a `{locale}` placeholder and one without — making it fully cache-safe and compatible with all modern Laravel packages.

- **[codezero-be/laravel-localized-routes](https://github.com/codezero-be/laravel-localized-routes) (deprecated)**  
  An  alternative to *laravel-localization*, using a **route-per-locale** approach (N× routes, one per language).  
  While that package is no longer maintained, many of its design ideas influenced this one.  
  Here, only **two routes** per definition are created — striking a balance between performance, maintainability, and flexibility.

- **[spatie/laravel-translatable](https://github.com/spatie/laravel-translatable)**  
  This package serves a different purpose — translating **Eloquent model fields**, not routes.  
  It works perfectly alongside this package if you want translatable slugs.


## Configuration

You can publish the configuration file with:

```bash
php artisan vendor:publish --provider="NielsNumbers\\LocaleRouting\\ServiceProvider" --tag=config
```

This will create a file at `config/locale-routing.php`.

---

### Configuration Options

| Key | Type | Default | Description |
|-----|------|----------|--------------|
| `supported_locales` | `array` | `[]` | List of all available locales. Example: `['en', 'de']`. |
| `hide_default_locale` | `bool` | `true` | If `true`, URLs using the **default (fallback)** locale will be redirected to the version **without** a locale prefix. Example: `/en/about` → `/about`. |
| `persist_locale.session` | `bool` | `true` | If `true`, the detected locale is stored in the session. |
| `persist_locale.cookie` | `bool` | `true` | If `true`, the detected locale is stored in a browser cookie. |
| `detectors` | `array` | `[UserDetector::class, BrowserDetector::class]` | Ordered list of classes used to detect a user’s locale when no locale is found in the URL, session, or cookie. |
| `redirect_enabled` | `bool` | `true` | Enables or disables automatic redirects between prefixed and unprefixed routes. |

---

### Detectors

Detectors are only used when no locale is found in the URL, session, or cookie.  
Each detector class implements a simple interface that returns a locale string or `null`.

By default, two detectors are provided:

1. **UserDetector** – reads the locale from the authenticated user model (if available).
2. **BrowserDetector** – detects the preferred language from the `Accept-Language` HTTP header.

You can register your own detectors by adding them to the `detectors` array in the configuration.  
They are executed in the order they appear — the first one returning a locale stops the chain.

Example:
```php
'detectors' => [
    \App\Locale\CustomDetector::class,
    \NielsNumbers\LocaleRouting\Detectors\UserDetector::class,
    \NielsNumbers\LocaleRouting\Detectors\BrowserDetector::class,
],
```

---

### Redirects

If `redirect_enabled` is set to `true`, the package automatically redirects between localized and non-localized URLs.

#### Behavior

1. If `hide_default_locale` is `true` and the current locale is 'en' and the **fallback_locale**,  
   requests to `/en/about` will redirect to `/about`.

   This prevents SEO duplicate content (both `/about` and `/en/about` pointing to the same page).

2. If the current locale is **not** the fallback_locale and the route has **no locale prefix**,  
   the request will be redirected to the localized version.  
   For example, if the user’s session locale is `de` and they open `/about`,  
   it will redirect to `/de/about`.

To disable redirects entirely, set:
```php
'redirect_enabled' => false,
```
> **Note:** Disabling redirects is strongly discouraged for normal web apps.
Without redirects, the application may display the wrong locale or produce duplicate URLs.
This option is primarily for headless APIs or advanced SPA setups.
---

### Notes on `app.locale` and `app.fallback_locale`

- `config('app.fallback_locale')` defines the **true default locale** for your application.  
  It must be set in your `config/app.php`, for example:
  ```php
  'fallback_locale' => 'en',
  ```

- `config('app.locale')` **starts** as the same value but is updated at runtime by `App::setLocale()`  
  (e.g. when the middleware detects `de`).  
  Because of this, its initial value in `config/app.php` **has no lasting effect** after the middleware runs.

- The **fallback locale** is used:
  1. As the base language for missing translations (`__()` and `@lang()` helpers).
  2. As the reference for the `hide_default_locale` setting.  
     For example, if `fallback_locale = 'en'` and the current locale is `'en'`,  
     `/en/about` will redirect to `/about`.


## Testing

This package includes a Docker setup for consistent testing across environments.

### Prerequisites
- Docker
- Docker Compose
- GNU Make (optional, but recommended)

### Usage with Make

The following will first build the docker image,
then install dependencies via composer and then run phpunit.

```bash
make build    # Build the Docker image
make install  # Install Composer dependencies inside the container
make test     # Run PHPUnit tests (tests are in /tests, using Orchestra Testbench)
``` 

### Usage without Make

If you don’t have `make`, you can run the commands manually:

```bash
docker compose build
UID=$(id -u) GID=$(id -g) docker compose run --rm test composer install
UID=$(id -u) GID=$(id -g) docker compose run --rm test vendor/bin/phpunit
```

## Background

This package is the maintained continuation of [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization).  
I (Adam Nielsen) was a collaborator on the original package, and since @mcamara has moved on from Laravel, I am now maintaining the route localization package.
The original package from mcamara has a very long legacy.

The [original package](https://github.com/mcamara/laravel-localization) generated **dynamic routes**,
which led to cache and compatibility issues.  
[laravel-localized-routes](https://github.com/codezero-be/laravel-localized-routes) solved this by generating **static routes for each locale** (N× per definition).

This package takes a **middle path**: each route is registered **twice** —  
once with a `{locale}` placeholder, and once without.  
This avoids dynamic routing issues while keeping the number of routes manageable.

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
