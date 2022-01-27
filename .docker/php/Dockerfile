FROM php:8.1.1-fpm-alpine

WORKDIR /var/www

RUN apk update && apk add --update --no-cache \
    autoconf g++ libtool make build-base freetype-dev libpng-dev jpeg-dev \
    libjpeg-turbo-dev freetype-dev oniguruma-dev libzip-dev zip jpegoptim \
    optipng pngquant gifsicle vim unzip git curl mysql-client

RUN rm /var/cache/apk/*

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install opcache bcmath pdo_mysql mbstring zip exif pcntl gd
RUN docker-php-ext-configure opcache --enable-opcache

RUN mkdir -p /usr/src/php/ext/redis; \
	curl -fsSL https://pecl.php.net/get/redis --ipv4 | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
	docker-php-ext-install redis;

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p storage/app/public/profile
COPY public/images/avatar.png storage/app/public/profile/avatar.png

RUN addgroup -g 1000 www
RUN adduser -u 1000 www -G www -D

COPY . .
COPY .env.example .env
COPY ./.docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN composer install

RUN chmod -R 775 storage
RUN chmod -R 775 bootstrap

RUN chmod +x .docker/php/start.sh
RUN php artisan storage:link

RUN chown www:www -R .
USER www

EXPOSE 9000
CMD ["php-fpm"]
