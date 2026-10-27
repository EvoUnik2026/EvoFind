FROM php:8.3-fpm-alpine
RUN apk add --no-cache icu-dev libzip-dev git unzip $PHPIZE_DEPS linux-headers \
 && docker-php-ext-install intl pdo_mysql opcache zip \
 && pecl install redis && docker-php-ext-enable redis \
 && apk del $PHPIZE_DEPS
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
WORKDIR /var/www/html
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
