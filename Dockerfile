# Utiliser une image de base PHP avec Apache
FROM php:8.2-apache AS build

# Définir le répertoire de travail
WORKDIR /var/www/html/

# Copier les fichiers de l'application
COPY . .


# Installer les dépendances avec Composer
RUN apt-get update \
    && apt-get install -y git \
    && apt-get clean \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --optimize-autoloader

# Exposition du port 8088 pour le serveur web
EXPOSE 8088

# Configurer le serveur web (Apache)
CMD ["apache2-foreground"]





