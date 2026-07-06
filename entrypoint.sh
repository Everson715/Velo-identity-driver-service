#!/bin/sh
set -e

# Generate RSA keys if they don't exist
KEYS_DIR="/var/www/html/storage"
mkdir -p "$KEYS_DIR"

if [ ! -f "$KEYS_DIR/oauth-private.key" ] || [ ! -f "$KEYS_DIR/oauth-public.key" ]; then
    echo "Generating RSA keys for JWT..."
    openssl genpkey -algorithm RSA -out "$KEYS_DIR/oauth-private.key" -pkeyopt rsa_keygen_bits:2048
    openssl rsa -pubout -in "$KEYS_DIR/oauth-private.key" -out "$KEYS_DIR/oauth-public.key"
    echo "RSA keys generated."
fi

# Fix permissions
chown -R www-data:www-data "$KEYS_DIR"
chmod 600 "$KEYS_DIR/oauth-private.key"
chmod 644 "$KEYS_DIR/oauth-public.key"

# Copy .env from .env.example if not exists
if [ ! -f "/var/www/html/.env" ]; then
    echo "Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
    php artisan key:generate --force
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Wait for PostgreSQL database connection to be ready
echo "Waiting for database to be ready..."
until php -r "try { new PDO('pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
    echo "Database not ready yet, retrying in 2 seconds..."
    sleep 2
done
echo "Database is ready!"

# Run migrations and db seed
echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force


# Execute the main command
exec "$@"
