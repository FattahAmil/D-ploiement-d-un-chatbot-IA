# image  PHP 8.2 avec Apache
FROM php:8.2-apache

# Mettre à jour et installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli

# Copier les fichiers locaux dans le répertoire du conteneur
COPY . /var/www/html/

# Définir les permissions 
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# éviter le message d'avertissement sur le ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Activer le module mod_rewrite d'Apache 
RUN a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# (foreground) le conteneur reste en fonctionnement
CMD ["apache2ctl", "-D", "FOREGROUND"]
