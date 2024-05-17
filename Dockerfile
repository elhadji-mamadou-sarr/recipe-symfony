# Utiliser une image de base PHP avec Apache
FROM php:8.2-apache AS build

# Définir le répertoire de travail
WORKDIR /var/www/html/

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances avec Composer
RUN apt-get update \
    && apt-get install -y git \
    && apt-get clean

# Installation des dépendances PHP via Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-scripts --no-autoloader

# Chargement de l'autoloader Symfony
RUN composer dump-autoload --optimize

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;

# Exposition du port 80 pour le serveur web
EXPOSE 80

# Commande de démarrage du serveur Apache
CMD ["apache2-foreground"]
