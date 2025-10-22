# Troubleshooting (PhpStorm + Docker + PHPUnit)

This guide helps fix common issues when running tests in PhpStorm using Docker Compose.

---

## Symptoms

During test runs you may see:

    Warning: file_put_contents(/app/.phpunit.result.cache): Permission denied

or:

    ERROR: No container found for test_1

---

## Causes

- **Permission denied**: Container user UID/GID does not match your host user; the container cannot write to the mounted project.
- **No container found**: The `test` container is not running (starts and exits immediately) or PhpStorm can’t attach to it.

---

## Fix: Set correct permissions (UID/GID)

1) Create a local `.env` and set your host IDs (check with `id -u` and `id -g`):

   cp .env.example .env

   UID=1000
   GID=1000

2) Rebuild and start the container:

   docker compose down
   docker compose build --no-cache
   docker compose up -d test

3) (Once) recreate the PHPUnit cache file if needed:

   rm -f .phpunit.result.cache
   touch .phpunit.result.cache
   chmod 666 .phpunit.result.cache

---

## Fix: Keep the container running

Ensure your `docker-compose.yml` prevents immediate exit, for example:

    command: tail -f /dev/null

Then start the service:

    docker compose up -d test

---

## PhpStorm configuration (quick overview)

- **Interpreter**: Settings → PHP → CLI Interpreter → Add → From Docker Compose → select service `test`.
- **PHPUnit**: Settings → PHP → Test Frameworks → Add → PHPUnit (by Remote Interpreter) → Autoloader path:

      /app/vendor/autoload.php

- Optional: set interpreter lifecycle to execute in an existing container instead of starting new ones.

---

## Verify

- Check your host IDs:

      id -u
      id -g

- Check the container is running:

      docker ps

- Run PHPUnit inside the container:

      docker compose exec test vendor/bin/phpunit

---

## Useful commands

- Open a shell:

      docker compose exec test bash

- View logs:

      docker compose logs -f test

- Stop containers:

      docker compose down

---

## Notes

- When PhpStorm starts Docker Compose directly (not via your Makefile), it won’t inherit `UID`/`GID` from your shell. The `.env` file ensures consistent IDs.
- If your host user is not `1000:1000`, update `.env` accordingly; otherwise write issues will persist.
