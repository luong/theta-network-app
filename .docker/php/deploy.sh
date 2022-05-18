#!/bin/sh

sudo git pull origin main -f

sudo docker-compose -f docker-compose.yml exec -T app composer install

sudo docker-compose -f docker-compose.yml exec -T app php artisan migrate --force
