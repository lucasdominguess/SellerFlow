# --- Estágio 1: Dependências (Composer) ---
FROM php:8.4-cli-alpine AS vendor
WORKDIR /app

# Instala pacotes básicos que o Composer geralmente precisa para baixar dependências
RUN apk add --no-cache git unzip

# Copia apenas o binário do Composer da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia arquivos do composer e instala dependências sem os scripts
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Copia o resto do código e gera o autoloader otimizado
COPY . .
# CORREÇÃO: Adicionado --no-scripts para evitar rodar o artisan no diretório temporário (/app)
RUN composer dump-autoload --optimize --no-scripts

# --- Estágio 2: Runtime (Imagem final para a Render) ---
FROM php:8.4-fpm-alpine

# Instala Nginx, Supervisor e extensões PHP
# Instala pacotes de RUNTIME (permanentes) e cria um grupo virtual (.build-deps) para compilação
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-libs \
    icu-libs \
    libzip \
    libpng \
    bash \
    ca-certificates \
    && apk add --no-cache --virtual .build-deps \
    postgresql-dev \
    icu-dev \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install pdo_pgsql intl zip gd \
    && apk del .build-deps # Remove apenas o pacote virtual temporário

# Configurar PHP para produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    php -r "echo 'PHP Production Config Applied\n';"

WORKDIR /var/www

# Copia o código do projeto
COPY . .
# Copia a pasta vendor já montada do estágio 1
COPY --from=vendor /app/vendor/ ./vendor/

# CORREÇÃO: Garante que a pasta de cache exista, caso o git não tenha copiado
RUN mkdir -p /var/www/bootstrap/cache

# Copia os arquivos de configuração
COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Ajusta permissões
RUN chmod +x /usr/local/bin/entrypoint.sh && \
    chown -R 82:82 /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Criar usuário não-root para execução
USER www-data

# CORREÇÃO: Executa a descoberta de pacotes agora no lugar e com usuário corretos
RUN php artisan package:discover --ansi

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
