FROM oberd/php-8.0-apache
ARG SHA_COMPOSER=55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae
ENV ENV_COMPOSER_SHA=$SHA_COMPOSER
RUN export LANG=C.UTF-8
COPY . /var/www/
COPY ./app.conf /etc/apache2/sites-available/
# Install dependencies
RUN apt update
RUN apt install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

RUN apt install nano
RUN apt install -y ghostscript
RUN git config --global user.email "ervic@pullex.mx"
RUN git config --global user.name "Ervic Pérez"

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '$ENV_COMPOSER_SHA') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN mv composer.phar /usr/local/bin/composer
RUN php -r "unlink('composer-setup.php');"

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sL https://deb.nodesource.com/setup_12.x | bash -
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip

RUN apt-get install -y nodejs
RUN export PATH="$(yarn global bin):$PATH"
RUN a2enmod rewrite
RUN a2dissite 000-default.conf
RUN a2ensite app.conf
WORKDIR /var/www/
RUN cp /var/www/.bashrc /root/.bashrc
RUN apt install -y supervisor
RUN service supervisor start
RUN cp /var/www/websockets.conf /etc/supervisor/conf.d/websockets.conf
RUN cp /var/www/queue.conf /etc/supervisor/conf.d/queue.conf
RUN cp /var/www/supervisord.conf /etc/supervisor/supervisord.conf
RUN supervisord
