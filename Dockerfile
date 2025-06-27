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
    docker-php-ext-install pdo pdo_mysql zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

# Copia todo el código fuente
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Copia el script y dale permisos
COPY apache-start.sh /usr/local/bin/apache-start
RUN chmod +x /usr/local/bin/apache-start

# Usa el script como punto de entrada
CMD ["apache-start"]

EXPOSE 80
