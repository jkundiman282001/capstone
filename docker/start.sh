#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h mysql --silent; do
    sleep 1
done

echo "MySQL is ready!"

# Install Composer dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install
fi

# Generate application key if .env exists but key is not set
if [ -f ".env" ] && ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Set storage permissions
echo "Setting storage permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

echo "Setup complete!"

# Start PHP-FPM
exec php-fpm

