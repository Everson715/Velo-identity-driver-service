FROM php:8.4-fpm

# Install dependencies and required PHP extensions for PostgreSQL and Cryptography
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    openssl \
    libssl-dev \
    libsodium-dev \
    && docker-php-ext-install pdo_pgsql pgsql sodium

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Setup permissions
RUN mkdir -p storage bootstrap/cache storage/keys && \
    chown -R www-data:www-data /var/www/html

# Expose the application port
EXPOSE 8000

# Final command to serve the application
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
