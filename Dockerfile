FROM php:8.3-apache

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libonig-dev \
    && docker-php-ext-install pdo_mysql intl zip opcache mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Apache: serve the Symfony public/ directory, falling back to index.php for all routes
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first for better layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

EXPOSE 80

# Commands for managing the Docker containers
# docker compose up -d          # démarrer
# docker compose logs -f app    # voir les logs
# docker compose down           # arrêter
