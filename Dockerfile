FROM php:8.2-fpm

# Instalar extensiones necesarias para Laravel
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
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# Instalar Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer carpeta de trabajo
WORKDIR /var/www

# Copiar todos los archivos del proyecto Laravel
COPY . .

# Instalar dependencias de Laravel
RUN composer install

# Exponer puerto (opcional, Laravel suele ejecutarse con php artisan serve)
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]