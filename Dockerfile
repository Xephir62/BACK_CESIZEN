FROM php:8.3.10-apache

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql intl zip opcache mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-enable opcache

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-production.ini

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var \
    && chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost/ || exit 1

# Commands for managing the Docker containers
# docker compose up -d          # démarrer
# docker compose logs -f app    # voir les logs
# docker compose down           # arrêter
