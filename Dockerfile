FROM php:7.4-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Download script to install PHP extensions and dependencies
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN DEBIAN_FRONTEND=noninteractive apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -qq \
      curl \
      git \
      zip unzip \
    && install-php-extensions \
      bcmath \
      bz2 \
      calendar \
      exif \
      gd \
      intl \
      ldap \
      memcached \
      mysqli \
      opcache \
      pdo_mysql \
      pdo_pgsql \
      pgsql \
      redis \
      soap \
      xsl \
      zip \
      sockets \
      pdo_sqlsrv \
      sqlsrv

# Install and configure XDebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    rm -rf /tmp/pear

# Install composer
RUN curl -fsSL https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer global require phpunit/phpunit --no-progress --no-scripts --no-interaction \
    && composer require illuminate/database

# Set the work directory to /var/www/html so all subsequent commands in this file start from that directory.
# Also set this work directory so that it uses this directory everytime we use docker exec.
WORKDIR /var/www/html

# Install the composer dependencies (no autoloader yet as that invalidates the docker cache)
COPY composer.json ./
COPY composer.lock ./
RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress -q && \
    composer clear-cache -q

# Bundle source code into container. Important here is that copying is done based on the rules defined in the .dockerignore file.
COPY . /var/www/html

# Dump the autoloader
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev -q

# Give apache write access to host
RUN chown -R www-data:www-data /var/www/html

COPY ./.docker/var/phpunit /usr/local/bin/
RUN chmod 755 /usr/local/bin/phpunit