FROM php:7.3-apache

#Add Laravel necessary php extensions
RUN set -xe \
    && apt-get update \
    && apt-get install -y cron unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev redis-server yarn\
    && pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis\
    && docker-php-ext-install -j$(nproc) zip mysqli pdo_mysql \
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

# Create working directory
RUN mkdir -p /var/www
ENV APACHE_DOCUMENT_ROOT /var/www/public

# Install composer from image. You may change it to the latest
COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer

RUN composer require laravel/ui

RUN composer require predis/predis ~1.0

RUN composer require laracasts/flash

RUN composer require laravel/passport

RUN composer require jeroennoten/laravel-adminlte

RUN composer require lucascudo/laravel-pt-br-localization

WORKDIR /var/www

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Make laravel feel comfortable with mod-rewrite
RUN a2enmod rewrite && service apache2 restart
EXPOSE 80