FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    zip \
    unzip \
    git \
    supervisor \
    apt-transport-https \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install FreeTDS for legacy SQL Server TLS compatibility
RUN apt-get update && apt-get install -y \
    freetds-dev \
    freetds-bin \
    unixodbc-dev \
    $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/*

# Configure and install the PDO dblib driver
RUN docker-php-ext-configure pdo_dblib \
    && docker-php-ext-install pdo_dblib

# Allow legacy TDS protocol version for older SQL Server compatibility
RUN echo "\n[global]\n\ttds version = 7.0\n" >> /etc/freetds/freetds.conf

# Install PHP extensions
RUN docker-php-ext-install \
        pdo \
        mbstring \
        exif \
        pcntl \
        bcmath \
        zip

RUN docker-php-ext-enable opcache

RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .



RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
