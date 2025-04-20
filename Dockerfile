# Utiliser une image officielle PHP avec Apache
FROM php:8.2-apache

# Mettre à jour et installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli



# Copier les fichiers de l'application dans le répertoire /var/www/html
COPY . /var/www/html/

# Définir les permissions pour que Apache puisse accéder aux fichiers
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html



# Ajouter une configuration pour éviter le message d'avertissement sur le ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Activer le module mod_rewrite d'Apache (utile pour des redirections)
RUN a2enmod rewrite

# Exposer le port 80 pour accéder à l'API
EXPOSE 80

# Démarrer Apache en mode foreground pour que le conteneur reste en fonctionnement
CMD ["apache2ctl", "-D", "FOREGROUND"]
