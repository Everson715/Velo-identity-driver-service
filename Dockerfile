FROM php:8.4-fpm

# Install dependencies and required PHP extensions for PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Setup permissions
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

# Expose the application port
EXPOSE 8000

# Final command to serve the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
