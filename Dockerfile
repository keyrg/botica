# Imagen base con PHP y Apache
FROM php:8.2-apache

# Habilita mod_rewrite y otras extensiones necesarias
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && a2enmod rewrite \
    && service apache2 restart

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Copia el código del proyecto al contenedor
COPY . /var/www/html/

# Establece permisos adecuados
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Establece el DocumentRoot si usas alguna subcarpeta (opcional)
# RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Expone el puerto 80 para Apache
EXPOSE 80
