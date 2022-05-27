#!/bin/sh

git pull origin main -f

docker-compose -f docker-compose.yml exec -T app composer install
docker-compose -f docker-compose.yml exec -T app php artisan migrate --force
docker-compose -f docker-compose.yml exec -T app php artisan theta:start
