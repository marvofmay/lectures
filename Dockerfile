FROM php:8.2-apache

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      procps \
      nano \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      cron \
      sudo \
      libzip-dev \
      wget \
      librabbitmq-dev \
      libpng-dev \
      libjpeg-dev \
      libfreetype6-dev \
      curl \
      gnupg \
      ca-certificates \
      pkg-config \
    && rm -rf /tmp/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Instalacja MongoDB dla PHP
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Instalacja AMQP
RUN docker-php-source extract \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-source delete

# **Sprawdzenie, czy amqp.ini istnieje i jest poprawnie ładowane**
RUN echo "extension=amqp.so" > /usr/local/etc/php/conf.d/20-amqp.ini

# Instalacja rozszerzeń PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
      sockets \
      intl \
      opcache \
      zip \
      gd \
    && rm -rf /tmp/*

# Instalacja Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Instalacja Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./ ./

# Kopiowanie pliku konfiguracyjnego Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Włączenie mod_rewrite
RUN a2enmod rewrite

# Uruchomienie Apache
CMD ["apache2-foreground"]