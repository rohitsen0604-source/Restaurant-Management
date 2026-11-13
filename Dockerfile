FROM php:8.0-apache

# Fix Debian Bullseye archive repositories (Debian Bullseye moved to archive.debian.org)
RUN echo "deb http://archive.debian.org/debian bullseye main contrib non-free" > /etc/apt/sources.list \
    && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99ignore-valid-until

# Use official php-extension-installer for reliable, error-free extension builds
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd zip mysqli pdo_mysql

# Enable Apache modules: mod_rewrite for clean URLs, headers for security/cache headers
RUN a2enmod rewrite headers

# Configure Apache Directory to allow .htaccess overrides and pass env vars
RUN printf '<Directory /var/www/html>\n    Options -Indexes +FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\nPassEnv DB_HOST DB_USER DB_PASS DB_PASSWORD DB_NAME DB_DATABASE DB_PORT DB_SSL DATABASE_URL MYSQL_URL CI_ENV ENVIRONMENT BASE_URL ENCRYPTION_KEY\n' > /etc/apache2/conf-available/codeigniter.conf \
    && a2enconf codeigniter

# Custom PHP settings for uploads, timeouts, timezone, environment variables and logging
RUN printf 'variables_order = "EGPCS"\nupload_max_filesize = 64M\npost_max_size = 64M\nmemory_limit = 256M\nmax_execution_time = 300\ndate.timezone = UTC\nlog_errors = On\nerror_log = /dev/stderr\ndisplay_errors = On\ndisplay_startup_errors = On\n' > /usr/local/etc/php/conf.d/custom.ini

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy project files
COPY cianos_system/cianos_system/ /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/uploads \
    && chmod -R 775 /var/www/html/application/cache \
    && chmod -R 775 /var/www/html/application/logs

# Render sets PORT env variable (typically 10000 or dynamic)
EXPOSE 80 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
