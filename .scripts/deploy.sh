#!/bin/bash

# Exit on error on some step
set -e

# Move to app directory
cd /home/ubuntu/abz-middle/laravel

echo "Pulling latest changes from the repository..."
git pull origin $(git rev-parse --abbrev-ref HEAD)

#echo "Setting up environment variables..."
#cp .env.example .env

echo "Installing dependencies using Composer..."
composer install --no-dev --optimize-autoloader

#echo "Generating application key..."
#php artisan key:generate

echo "Running database migrations..."
php artisan migrate

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed successfully!"
