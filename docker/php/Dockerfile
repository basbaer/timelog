FROM php:8.3-apache
USER root
WORKDIR /var/www/html

RUN apt-get update && \
    apt-get install -y vim wget

RUN a2enmod rewrite

# Install Xdebug via pecl
RUN apt-get install -y autoconf && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

# AllowOverride for .htaccess (enable .htaccess support)
RUN sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf && \
    sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/sites-available/000-default.conf || true

# Rename php.ini-production to php.ini
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Concatenate custom configurations to php.ini and php.ini-production
RUN echo "memory_limit = 4000M" >> /usr/local/etc/php/php.ini && \
    echo "max_execution_time = 3000" >> /usr/local/etc/php/php.ini && \
    echo "max_input_time = 600" >> /usr/local/etc/php/php.ini && \
    echo "upload_max_filesize = 200M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size = 400M" >> /usr/local/etc/php/php.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini

# Enable Apache modules and configure AllowOverride
RUN a2enmod rewrite

RUN service apache2 restart

RUN touch /usr/local/etc/php/conf.d/xdebug.ini

# Do not force-enable extensions in php.ini; docker-php-ext-install handles this via conf.d

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    ca-certificates \
    curl \
    gnupg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        mbstring \
        bcmath \
        exif \
        intl

# Install Node.js 20 + npm
RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
      | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" \
      > /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    node -v && npm -v

# Install Composer (from official Composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf && \
    sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf