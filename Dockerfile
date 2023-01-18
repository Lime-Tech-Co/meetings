FROM php:8.2-fpm

# Install PHP extensions
RUN apt-get update && \
    apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        zip \
        unzip \
        git

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && docker-php-ext-install exif

# Installations
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get -y install yarn && apt-get -y install sqlite3
RUN yarn global add apidoc

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer global require friendsofphp/php-cs-fixer

RUN mkdir /app
COPY . /app
COPY uploads.ini /usr/local/etc/php/conf.d/

WORKDIR /app

RUN composer install
RUN composer dump-autoload

ENV APP_URL=http://localhost

ENV APP_ENV=local

ENV PHP_CS_FIXER_IGNORE_ENV=1