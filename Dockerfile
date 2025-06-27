FROM php:8.2-apache

# Instala dependencias
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev && \
    docker-php-ext-install pdo pdo_mysql zip && \
    a2enmod rewrite

# Habilita .htaccess en Apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

# Copia el código del proyecto
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
