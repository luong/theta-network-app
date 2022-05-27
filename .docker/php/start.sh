#!/bin/sh

chmod -R 777 storage
chmod -R 777 bootstrap
chown www:www -R .

crond -b
php-fpm
