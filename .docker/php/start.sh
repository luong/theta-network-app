#!/bin/sh

chmod -R 775 storage
chmod -R 775 bootstrap
chown www:www -R .

crond -b
php-fpm
