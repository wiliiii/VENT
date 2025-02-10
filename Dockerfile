# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilita el módulo de reescritura de Apache (si usas .htaccess)
RUN a2enmod rewrite

# Copia todos los archivos del proyecto al contenedor
COPY . /var/www/html

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto 80
EXPOSE 80

# Inicia Apache
CMD ["apache2-foreground"]
