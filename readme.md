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

> Note: This package does not make Eloquent models translatable.  
> For that, see [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable).

## Background

This package is the maintained continuation of [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization).  
I (Adam Nielsen) was a collaborator on the original package, and since @mcamara has moved on, I am now maintaining it as version v3.

The [original package](https://github.com/mcamara/laravel-localization) generated **dynamic routes**, 
which led to cache and compatibility issues.  
[laravel-localized-routes](https://github.com/codezero-be/laravel-localized-routes) solved this by generating **static routes for each locale** (N× per definition).

This package takes a **middle path**: each route is registered **twice** —  
once with a `{locale}` placeholder, and once without (using a `no_prefix.` name).  
This avoids dynamic routing issues while keeping the number of routes manageable.

## Do I need this package?

Use this package if you want:

- automatic locale detection from the request (e.g. from the browser)
- automatic redirects to localized routes
- fully translatable routes (e.g. `/en/humans`, `/de/menschen`, etc.)

You **don’t** need it if you are fine with only:

- `example.com/de/blog`
- `example.com/en/blog`

and do not need `example.com/blog` or locale detection from the browser.

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