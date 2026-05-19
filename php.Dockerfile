FROM php:8.2-fpm-alpine

# Update apk and install required dependencies
RUN apk add --no-cache \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli \
    && docker-php-ext-enable pdo_mysql mysqli

# Set working directory
WORKDIR /app


