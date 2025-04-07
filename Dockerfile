FROM php:8.1-fpm

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++ libssl-dev libpng-dev libjpeg-dev libfreetype6-dev libzip-dev ghostscript libxml2-dev 
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install iconv \
    && docker-php-ext-install simplexml \
    && docker-php-ext-install xml 
    
    
# Instalar npm
# RUN apt-get install -y npm

# Install Node.js 
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs


RUN >> /usr/local/etc/php/php.ini
# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Update Php Settings
RUN sed -E -i -e 's/max_execution_time = 30/max_execution_time = 120/' /usr/local/etc/php/php.ini \
 && sed -E -i -e 's/post_max_size = 8M/post_max_size = 31M/' /usr/local/etc/php/php.ini \
 && sed -E -i -e 's/upload_max_filesize = 2M/upload_max_filesize = 30M/' /usr/local/etc/php/php.ini
 # Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#Install and Configure Xdebug
# RUN pecl install xdebug-3.1.6 \
