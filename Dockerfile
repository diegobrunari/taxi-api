# Usar a imagem PHP oficial com Apache
FROM php:8.0-apache

# Instalar extensões PHP necessárias para o MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos da pasta 'src' para o diretório de trabalho no container
COPY ./src/ /var/www/html/

# Expor a porta 80
EXPOSE 80
