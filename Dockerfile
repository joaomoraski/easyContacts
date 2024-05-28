# Get the docker hub image with apache
FROM php:8.3-apache

# Install some dependencies to run the project with docker
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-install -j$(nproc) \
    mbstring \
    curl \
    xml \
    pdo_mysql \
    gd


# send env variables
ENV DATABASE_HOST="sql.easycontacts.com"
ENV DATABASE_USER="moraski"
ENV DATABASE_PASSWORD="moraski"
ENV DATABASE_NAME="easyContacts"

# install composer(You need to have composer installed on your computer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

# Copy the certificates and key to apche conf
COPY deploy/easycontacts.crt /etc/ssl/certs/easycontacts.crt
COPY deploy/easycontacts.key /etc/ssl/private/easycontacts.key

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 600 /etc/ssl/private/easycontacts.key \
    && chmod 644 /etc/ssl/certs/easycontacts.crt

# enable some mods and change the path to the certs
RUN a2enmod rewrite ssl \
    && a2ensite default-ssl \
    && sed -i 's|SSLCertificateFile.*|SSLCertificateFile /etc/ssl/certs/easycontacts.crt|' /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's|SSLCertificateKeyFile.*|SSLCertificateKeyFile /etc/ssl/private/easycontacts.key|' /etc/apache2/sites-available/default-ssl.conf 

# open the http and https ports
EXPOSE 80 443

# init apache
CMD ["apache2-foreground"]
