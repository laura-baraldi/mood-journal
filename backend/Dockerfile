FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    bcmath \
    opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN sed -ri \
    's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf \
    && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' \
    >> /etc/apache2/apache2.conf \
    && sed -ri 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

CMD ["apache2-foreground"]