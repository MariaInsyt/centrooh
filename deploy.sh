#!/bin/sh

set -e

# Turn on maintenance mode
php artisan down --retry=60 --refresh=60

# Pull the latest changes from the git repository
git pull

# Install and update composer dependencies
composer install --no-interaction --no-dev --prefer-dist

# NPM install and build
npm install 
npm run build

# Run database migrations
php artisan migrate 

php artisan optimize:clear

# Turn off maintenance mode
php artisan up

echo "Deployment successful"