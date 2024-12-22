# Dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron
    
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www

RUN echo "* * * * * cd /var/www && /usr/local/bin/php artisan app:fetch-articles >> /var/www/cronjob.log 2>&1" > /etc/cron.d/laravel-schedule
RUN chmod 0644 /etc/cron.d/laravel-schedule
RUN crontab /etc/cron.d/laravel-schedule


CMD cron && php-fpm