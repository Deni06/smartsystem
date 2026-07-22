# Gunakan image PHP dengan Apache
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Update dan install dependencies yang diperlukan
RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions yang diperlukan
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    curl

# Enable Apache rewrite module (untuk .htaccess)
RUN a2enmod rewrite

# Izinkan .htaccess di /var/www/html dibaca (default AllowOverride None
# membuat .htaccess diam-diam diabaikan tanpa error apapun)
COPY config/apache-overrides.conf /etc/apache2/conf-enabled/zzz-overrides.conf

# Pastikan direktori session PHP ada & writable (default image tidak punya ini,
# menyebabkan session_start() gagal silently -> login tidak persisten)
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

# Bake custom php.ini settings into the image
COPY config/php.ini /usr/local/etc/php/conf.d/zz-custom.ini

# Copy project files ke image
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Avoid "Could not reliably determine the server's FQDN" warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Default values (NON-sensitif saja; password TIDAK di-bake ke image layer).
ENV MYSQL_HOST=db \
    MYSQL_USER=smartdoor \
    MYSQL_DATABASE=smartdoor_db \
    MQTT_HOST=localhost \
    MQTT_PORT=1883 \
    MQTT_USERNAME=admin_pintu \
    MQTT_WS_PORT=443 \
    MQTT_WS_PATH=/mqtt \
    MQTT_WS_USE_SSL=1

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -fs http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]
