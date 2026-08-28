# ---- Base image: PHP 8.4 (matches local Herd version), Debian-based, PHP-FPM already set up ----
FROM php:8.4-fpm

# ---- System-level libraries our PHP extensions need to be built against ----
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# ---- PHP extensions Laravel/our app needs that aren't built in by default ----
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# ---- Composer itself, borrowed straight from its official image (no compiling needed) ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- A dedicated, low-privilege user to actually run the app as (never root) ----
RUN groupadd -g 1000 appuser && useradd -u 1000 -ms /bin/bash -g appuser appuser

# ---- Every instruction below happens inside this folder ----
WORKDIR /var/www/html

# ---- Bring our actual Laravel code into the image (.dockerignore filters out the junk) ----
COPY . .

# ---- Install PHP dependencies fresh, for this Linux environment (dev tools included for local work) ----
RUN composer install --optimize-autoloader --no-interaction

# ---- Hand ownership to our app user, and make Laravel's writable folders actually writable ----
RUN chown -R appuser:appuser /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ---- Stop running as root; everything from here on runs as our low-privilege user ----
USER appuser

# ---- Documents that PHP-FPM listens on port 9000 (informational only, doesn't publish it) ----
EXPOSE 9000
