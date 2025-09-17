FROM seedsstd/seeds_apache_php:8.3-minimum
RUN docker-php-ext-install -j$(nproc) mysqli pdo_mysql

RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y \
    libzip-dev

RUN docker-php-ext-install \
    zip
    
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

RUN a2enmod rewrite