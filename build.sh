#!/bin/sh
cd code
composer install # composer has to be installed and configured
php artisan api-platform:install # php8.3 hast to be installed and configured
touch database/database.sqlite
php artisan migrate:install --silent
php artisan migrate --force
php artisan db:seed --force
cd ..
docker build -t palamar/parking:latest .
# now you can run:
#docker compose up