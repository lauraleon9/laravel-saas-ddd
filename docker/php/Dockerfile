FROM php:8.4-fpm

# Instalar dependencias del sistema
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        libcurl4-openssl-dev \
        pkg-config \
        libssl-dev \
    ; \
    # Instalar extensiones PHP
    docker-php-ext-install \
        pdo_mysql \
        mbstring \
        xml \
        pcntl \
        bcmath \
        zip \
        gd \
        curl \
    ; \
    # Limpiar cache de apt
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*


# Composer (copiamos desde imagen oficial para rapidez)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Opcional: aumentar memory_limit en dev
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

WORKDIR /var/www/html
