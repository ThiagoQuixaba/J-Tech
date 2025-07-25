FROM php:8.2-apache

# Instala dependências para o CakePHP + PostgreSQL (Supabase)
RUN apt-get update && apt-get install -y \
    libicu-dev \
    unzip \
    git \
    zip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install intl pdo pdo_pgsql zip

# Habilita mod_rewrite
RUN a2enmod rewrite

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia o projeto
COPY . .

# Permite que Apache leia os arquivos
RUN chown -R www-data:www-data /var/www/html

# Ativa reescrita de URL no Apache
RUN echo '<Directory /var/www/html/webroot>\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf

# Expõe porta padrão do Apache
EXPOSE 80
