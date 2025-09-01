FROM webdevops/php-nginx:8.4-alpine

# Set Laravel doc root
ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV PHP_DATE_TIMEZONE=UTC
ENV PHP_MEMORY_LIMIT=512M
ENV PHP_POST_MAX_SIZE=50M
ENV PHP_UPLOAD_MAX_FILESIZE=50M

WORKDIR /var/www/html

# Install dependencies
RUN apk add --no-cache bash curl mysql-client nodejs npm composer

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy the application code
COPY . .

# Ensure env file exists
COPY .env.docker .env

# Nginx & PHP configs
COPY docker/nginx/default.conf /opt/docker/etc/nginx/vhost.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Fix permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/{cache,sessions,views} \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["/opt/docker/bin/entrypoint.sh", "supervisord"]
