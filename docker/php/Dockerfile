FROM php:8.2-apache
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

RUN echo "zend_extension=xdebug" >> /usr/local/etc/php/php.ini

RUN sed -i 's/;extension=mysqli/extension=mysqli/' /usr/local/etc/php/php.ini && \
    sed -i 's/;extension=pdo_mysql/extension=pdo_mysql/' /usr/local/etc/php/php.ini

# Configure Xdebug
RUN echo "zend_extension = /usr/local/lib/php/extensions/no-debug-non-zts-20220829/xdebug.so" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.profiler_output_dir=/tmp/xdebug/" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.profiler_enable=On" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_host=localhost" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf && \
    sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf