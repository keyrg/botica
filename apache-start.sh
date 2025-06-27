#!/bin/bash

# Habilitar mod_rewrite
a2enmod rewrite

# Asegurar AllowOverride All en Apache
sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Opcional: evitar el warning del ServerName
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Iniciar Apache en primer plano
apache2-foreground
