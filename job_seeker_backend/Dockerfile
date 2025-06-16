FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    nano \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer.json first
COPY composer.json ./

# Copy composer.lock if it exists, otherwise create empty file
COPY composer.loc[k] ./
RUN [ -f composer.lock ] || composer install --dry-run

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the application
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www

# Remove database operations from build (run separately)
# RUN php artisan migrate --force && \
#     php artisan db:seed --force

CMD php artisan serve --host=0.0.0.0 --port=8080
