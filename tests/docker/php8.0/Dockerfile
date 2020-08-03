FROM php:8.0-rc-fpm-alpine AS php_fpm

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp

RUN sed -i 's/#default_bits/default_bits/g; s/#default_md/default_md/g' /etc/ssl/openssl.cnf

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# ext-zip uses system libzip
RUN apk --no-cache add libzip-dev

RUN apk add --no-cache $PHPIZE_DEPS \
    && docker-php-ext-install zip

RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/cb19f2aa3aeaa2006c0cd69a7ef011eb31463067/web/installer \
 && php -r " \
    \$signature = '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5'; \
    \$hash = hash('sha384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
        unlink('/tmp/installer.php'); \
        echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
        exit(1); \
    }" \
    && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer \
    && rm -f /tmp/installer.php

RUN php -v && php -m && composer --ansi --version --no-interaction
RUN composer self-update --preview

RUN curl --silent --fail --location --retry 3 --output /usr/local/bin/pickle --url https://github.com/FriendsOfPHP/pickle/releases/download/v0.6.0/pickle.phar \
 && chmod +x /usr/local/bin/pickle

FROM php_fpm AS php_fpm_xdebug

#RUN pickle install xdebug \
#    && docker-php-ext-enable xdebug
RUN mkdir -p /usr/src/php/ext/xdebug && mkdir -p /tmp/xdebug \
    && curl --silent --fail --location --retry 3 --output /tmp/xdebug.zip --url https://github.com/xdebug/xdebug/archive/master.zip \
    && unzip /tmp/xdebug.zip -d /tmp/xdebug \
    && mv /tmp/xdebug/xdebug-master/* /usr/src/php/ext/xdebug \
    && docker-php-ext-install xdebug \
    && echo "xdebug.mode=coverage" >> "$PHP_INI_DIR/php.ini"

RUN php -v && php -m && composer --ansi --version --no-interaction


FROM php_fpm AS php_fpm_pcov

RUN pickle install pcov \
    && docker-php-ext-enable pcov

RUN php -v && php -m && composer --ansi --version --no-interaction
