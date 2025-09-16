FROM php:8.2-cli

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    unzip git zip libicu-dev && \
    docker-php-ext-install intl && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app
