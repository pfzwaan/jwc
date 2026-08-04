FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# The php:8.3-cli image ships no php.ini, so PHP falls back to its compiled
# defaults: upload_max_filesize=2M / post_max_size=8M. Any normal photo uploaded
# in the Filament admin exceeds that and is dropped before Laravel sees it.
RUN { \
        echo 'upload_max_filesize = 128M'; \
        echo 'post_max_size = 136M'; \
        echo 'memory_limit = 512M'; \
        echo 'max_execution_time = 120'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8002

ENTRYPOINT ["entrypoint.sh"]
