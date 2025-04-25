# Utiliser une image officielle PHP avec Apache
FROM php:8.2-apache

# Configurer les dossiers de log
RUN mkdir -p /var/log/inoui

# Mettre à jour et installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli \
    && rm -rf /var/lib/apt/lists/*  # Nettoyage pour réduire la taille de l'image

# Copier les fichiers de l'application dans le répertoire /var/www/html
COPY . /var/www/html/

# Définir les permissions pour Apache
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Ajouter une configuration pour éviter le message d'avertissement sur le ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Activer le module mod_rewrite d'Apache
RUN a2enmod rewrite

# Ajouter la configuration pour AllowOverride dans le bon contexte
RUN echo "<Directory /var/www/html>\nAllowOverride All\n</Directory>" >> /etc/apache2/apache2.conf

# Exposer le port 80 pour accéder à l'API
EXPOSE 80

# Démarrer Apache en mode foreground pour que le conteneur reste en fonctionnement
CMD ["apache2ctl", "-D", "FOREGROUND"]
