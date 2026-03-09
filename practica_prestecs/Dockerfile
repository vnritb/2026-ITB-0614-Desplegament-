FROM php:8.2-apache

# Actualizar la lista de paquetes
RUN apt-get update

# Instal·lar l'extensió MySQLi per a connexió a MariaDB
RUN docker-php-ext-install mysqli

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar els fitxers de la web al directori de Apache
COPY . /var/www/html/

# Establir els permisos correctes
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
