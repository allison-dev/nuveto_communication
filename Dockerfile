FROM php:7.3-apache

#Add Laravel necessary php extensions
RUN set -xe \
    && apt-get update \
    && apt-get install -y cron unzip supervisor python2.7 python-pip python2.7-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev redis-server yarn\
    && pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis\
    && docker-php-ext-install -j$(nproc) zip mysqli opcache pdo_mysql \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure bcmath --enable-bcmath \
    && docker-php-ext-configure mysqli --with-mysqli \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-configure mbstring --enable-mbstring \
    && docker-php-ext-install -j$(nproc) \
    gd \
    bcmath \
    mysqli \
    pdo_mysql \
    mbstring \
    pcntl

# Config opcache
COPY config/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Config Supervisor
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install composer from image. You may change it to the latest
COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer

RUN composer require laravel/ui

RUN composer require predis/predis ~1.0

RUN composer require laracasts/flash

RUN composer require laravel/passport

RUN composer require jeroennoten/laravel-adminlte

RUN composer require lucascudo/laravel-pt-br-localization

WORKDIR /var/www/html

#config apache
COPY src/ /var/www

#copy .env
COPY config/.env /var/www

#docker compose exe
RUN cd /var/www && rm -fr html && ln -s /var/www/public html && composer install

RUN composer require laravel/ui

#commands artisan
RUN cd /var/www && php artisan key:generate && php artisan ui bootstrap && sleep 1 && touch storage/logs/laravel.log && chmod -R 0777 storage/logs && chown -R www-data:www-data /var

#start services
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod deflate

# Expose port
EXPOSE 80

# Entrypoint
ENTRYPOINT ["sh", "init.sh"]