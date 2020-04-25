FROM php:cli

# Setup PHP
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) zip

# Checkout Nextcloud & Submodules
ARG DATE=unknown
WORKDIR /
RUN git clone --depth=1 https://github.com/nextcloud/server

WORKDIR /server
RUN git submodule sync --recursive && git submodule update --init --force --recursive --depth=1

# Setup Nextcloud
RUN mkdir data
RUN ./occ maintenance:install --verbose --database=sqlite --database-name=nextcloud --database-user=root --database-pass=rootpassword --admin-user admin --admin-pass password