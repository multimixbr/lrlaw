# Use a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instale as extensões do PHP necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip intl mysqli

# Habilitar o mod_rewrite do Apache
RUN a2enmod rewrite

# Copie os arquivos do projeto para o diretório do Apache
COPY . /var/www/html

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Dê permissões ao diretório writable
RUN chown -R www-data:www-data /var/www/html/writable
RUN chmod -R 775 /var/www/html/writable

# Exponha a porta 80 para o Apache
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]
