#!/bin/sh
set -e

# Generate RSA keys if they don't exist
KEYS_DIR="/var/www/html/storage/keys"
mkdir -p "$KEYS_DIR"

if [ ! -f "$KEYS_DIR/private.key" ] || [ ! -f "$KEYS_DIR/public.key" ]; then
    echo "Generating RSA keys for JWT..."
    openssl genpkey -algorithm RSA -out "$KEYS_DIR/private.key" -pkeyopt rsa_keygen_bits:2048
    openssl rsa -pubout -in "$KEYS_DIR/private.key" -out "$KEYS_DIR/public.key"
    echo "RSA keys generated."
fi

# Fix permissions
chown -R www-data:www-data "$KEYS_DIR"
chmod 600 "$KEYS_DIR/private.key"
chmod 644 "$KEYS_DIR/public.key"

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Execute the main command
exec "$@"
