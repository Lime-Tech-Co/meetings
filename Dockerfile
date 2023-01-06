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
        unzip

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && docker-php-ext-install exif

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a working directory
RUN mkdir /app
WORKDIR /app

# Copy the composer.json and composer.lock files
COPY composer.json composer.lock /app/

# Install dependencies
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy the rest of the codebase
COPY . /app

# Generate the autoloader
RUN composer dump-autoload --optimize

# Set the application URL
ENV APP_URL=http://localhost

# Set the application environment
ENV APP_ENV=local

# Set the application key
RUN php artisan key:generate --no-interaction

# Expose the application's port
EXPOSE 8000

# Run the application
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
