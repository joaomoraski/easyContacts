# Use a imagem base do PHP 8.3 com Apache
FROM php:8.3-apache

# Instale as extensões PHP necessárias
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

# Instale o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie os arquivos da aplicação para o diretório web do Apache
COPY . /var/www/html/

# Copie os certificados SSL para os locais apropriados
COPY deploy/easycontacts.crt /etc/ssl/certs/easycontacts.crt
COPY deploy/easycontacts.key /etc/ssl/private/easycontacts.key

# Ajuste permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 600 /etc/ssl/private/easycontacts.key \
    && chmod 644 /etc/ssl/certs/easycontacts.crt

# Configure o nome do servidor e habilite os módulos e o site SSL no Apache
RUN a2enmod rewrite ssl \
    && a2ensite default-ssl \
    && sed -i 's|SSLCertificateFile.*|SSLCertificateFile /etc/ssl/certs/easycontacts.crt|' /etc/apache2/sites-available/default-ssl.conf \
    && sed -i 's|SSLCertificateKeyFile.*|SSLCertificateKeyFile /etc/ssl/private/easycontacts.key|' /etc/apache2/sites-available/default-ssl.conf 

# Exponha as portas 80 e 443
EXPOSE 80 443

# Comando para iniciar o Apache
CMD ["apache2-foreground"]
