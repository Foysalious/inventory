FROM php:7.4.11-apache

COPY start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

RUN rm /etc/apt/preferences.d/no-debian-php

RUN apt-get update \
    && apt-get install -y libpng-dev zlib1g-dev git\
    && a2enmod headers \
    && a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

RUN docker-php-ext-install pdo pdo_mysql calendar

RUN pecl install redis && docker-php-ext-enable redis

RUN apt-get install -yqq \
           libzip-dev zip unzip && \
           docker-php-ext-configure zip; \
           docker-php-ext-install zip && \
           php -m | grep -q 'zip'

ENV PATH="~/.composer/vendor/bin:./vendor/bin:${PATH}"

CMD ["/usr/local/bin/start"]

