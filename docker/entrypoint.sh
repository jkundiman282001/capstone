#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Run migrations if needed (optional for local, but good to have)
# php artisan migrate --force

# Start PHP-FPM
exec php-fpm
