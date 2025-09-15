FROM php:8.2.9-fpm
ENV DEBIAN_FRONTEND=noninteractive
# Locale e deps
RUN apt-get update && apt-get install -y \
    apt-transport-https ca-certificates gnupg \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    locales \
    zip \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    nano \
    unzip \
    git \
    libpq-dev \
    libonig-dev \
    libsqlite3-dev \
    sqlite3 \
    wget \
    sudo \
    curl \
    postgresql-client \
    default-mysql-client \
    default-libmysqlclient-dev \
    net-tools \
    iputils-ping \
    procps \
    librdkafka-dev \
    hstr \
    libicu-dev \
    libxml2-dev \
    libsodium-dev \
    && rm -rf /var/lib/apt/lists/*

# --- 2) Microsoft ODBC + mssql-tools18 (Debian 12 / bookworm) ---
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends ca-certificates curl gnupg; \
    curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
      | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg; \
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
      > /etc/apt/sources.list.d/mssql-release.list; \
    apt-get update; \
    ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18; \
    echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' > /etc/profile.d/mssql-tools18.sh; \
    rm -rf /var/lib/apt/lists/*

# PHP extensions core
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd intl mbstring bcmath exif pcntl sockets opcache soap xml zip pdo pdo_mysql pdo_pgsql mysqli sodium

# --- 4) PECL: sqlsrv/pdo_sqlsrv (garantindo unixodbc-dev no MESMO RUN) ---
RUN set -eux; \
  apt-get update; \
  apt-get install -y --no-install-recommends unixodbc-dev; \
  pecl channel-update pecl.php.net; \
  # versões compatíveis com PHP 8.2 (pode remover o sufixo para pegar a mais nova)
  printf "\n" | pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0; \
  extdir="$(php-config --extension-dir)"; echo "extension_dir=$extdir"; \
  # sanity check: os .so precisam existir ou o build falha
  ls -l "$extdir"/sqlsrv.so "$extdir"/pdo_sqlsrv.so; \
  # habilita no CLI e FPM
  echo "extension=sqlsrv"     > /usr/local/etc/php/conf.d/20-sqlsrv.ini; \
  echo "extension=pdo_sqlsrv" > /usr/local/etc/php/conf.d/20-pdo_sqlsrv.ini; \
  # valida carregamento
  php -m | grep -i -E 'sqlsrv|pdo_sqlsrv'; \
  rm -rf /var/lib/apt/lists/*

# Redis, rdkafka, xdebug (opcional, desativado por padrão)
RUN pecl install redis \
    && pecl install rdkafka \
    && pecl install xdebug \
    && docker-php-ext-enable redis rdkafka xdebug

# Composer
ENV COMPOSER_HOME=/tmp/composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -f /tmp/composer-setup.php

# PHP INI básico
RUN { \
      echo "memory_limit=512M"; \
      echo "upload_max_filesize=64M"; \
      echo "post_max_size=64M"; \
      echo "max_execution_time=180"; \
      echo "date.timezone=America/Sao_Paulo"; \
      echo "opcache.enable=1"; \
      echo "opcache.jit=1255"; \
      echo "opcache.jit_buffer_size=128M"; \
      echo "xdebug.mode=off"; \
    } > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html
CMD ["php-fpm"]
