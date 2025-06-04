FROM php:8.1-apache

# Instala extensões PHP necessárias para o Laravel e o Krayin
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia todos os arquivos para dentro do container
COPY . /var/www/html

# Permissões de arquivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configura o DocumentRoot e permissões da pasta public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

# Instala o Composer e adiciona ao PATH
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Expõe a porta padrão
EXPOSE 80

# Comando de inicialização do Apache
CMD ["apache2-foreground"]
