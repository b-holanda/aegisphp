# =====================================================
# STAGE 1 — BUILD DO PHP (DEBIAN)
# =====================================================
FROM debian:bookworm AS builder

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential autoconf bison re2c pkg-config wget ca-certificates \
    libmariadb-dev \
    libxml2-dev \
    libsqlite3-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libzip-dev \
    libreadline-dev \
    libargon2-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    libfreetype6-dev \
    libicu-dev \
    libxslt1-dev \
    libldap2-dev \
    libkrb5-dev \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /usr/src

RUN wget -q https://www.php.net/distributions/php-8.5.2.tar.gz && \
    tar -xzf php-8.5.2.tar.gz && \
    rm php-8.5.2.tar.gz

WORKDIR /usr/src/php-8.5.2

RUN ./buildconf --force && \
    ./configure \
      --prefix=/opt/php-8.5.2 \
      --with-config-file-path=/opt/php-8.5.2/etc \
      --enable-fpm \
      --with-openssl \
      --with-zlib \
      --with-curl \
      --enable-mbstring \
      --enable-intl \
      --with-zip \
      --with-readline \
      --enable-soap \
      --enable-sockets \
      --enable-opcache \
      --enable-pcntl \
      --with-pdo-mysql \
      --with-mysqli && \
    make -j"$(nproc)" && \
    make install

# Composer
RUN wget -q -O /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar && \
    chmod +x /usr/local/bin/composer

# =====================================================
# STAGE 2 — DEV RUNTIME (COM FERRAMENTAS)
# =====================================================
FROM debian:bookworm AS dev

ENV DEBIAN_FRONTEND=noninteractive

# Ferramentas DEV + libs runtime
RUN apt-get update && apt-get install -y --no-install-recommends \
    bash \
    git \
    unzip \
    zip \
    curl \
    ca-certificates \
    procps \
    iputils-ping \
    net-tools \
    less \
    vim \
    # build tools (DEV) p/ possível pecl/ext depois
    build-essential autoconf pkg-config \
    # libs runtime do php
    libmariadb3 \
    libxml2 \
    libsqlite3-0 \
    libssl3 \
    libcurl4 \
    libonig5 \
    libzip4 \
    libreadline8 \
    libargon2-1 \
    libjpeg62-turbo \
    libpng16-16 \
    libwebp7 \
    libxpm4 \
    libfreetype6 \
    libicu72 \
    libxslt1.1 \
    libldap-2.5-0 \
    libkrb5-3 \
 && rm -rf /var/lib/apt/lists/*

# PHP compilado
COPY --from=builder /opt/php-8.5.2 /opt/php-8.5.2
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

ENV PATH=/opt/php-8.5.2/bin:/opt/php-8.5.2/sbin:$PATH
ENV PHP_INI_SCAN_DIR=/opt/php-8.5.2/etc/conf.d

# -------------------------
# USER DEV (alinha UID/GID)
# -------------------------
ARG APP_UID=1000
ARG APP_GID=1000

RUN groupadd -g ${APP_GID} appgroup \
 && useradd -m -u ${APP_UID} -g ${APP_GID} -s /bin/bash appuser

# Pastas de runtime DEV
RUN mkdir -p /app /tmp/php /tmp/composer \
 && chown -R appuser:appgroup /app /tmp/php /tmp/composer \
 && chmod -R 775 /tmp/php /tmp/composer

ENV COMPOSER_HOME=/tmp/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer/cache

# -------------------------
# PHP config DEV
# -------------------------
RUN mkdir -p /opt/php-8.5.2/etc/conf.d

# base ini (dev)
COPY --from=builder /usr/src/php-8.5.2/php.ini-development /opt/php-8.5.2/etc/php.ini

# overrides dev
RUN printf "%s\n" \
  "display_errors=1" \
  "display_startup_errors=1" \
  "error_reporting=E_ALL" \
  "log_errors=1" \
  "html_errors=1" \
  "memory_limit=512M" \
  "max_execution_time=0" \
  "date.timezone=UTC" \
  "" \
  "opcache.enable=1" \
  "opcache.validate_timestamps=1" \
  "opcache.revalidate_freq=0" \
  > /opt/php-8.5.2/etc/conf.d/99-dev.ini

# php-fpm (dev): roda em foreground e loga no stdout/stderr
RUN mkdir -p /opt/php-8.5.2/etc/php-fpm.d && \
  printf "%s\n" \
  "[global]" \
  "daemonize = no" \
  "error_log = /proc/self/fd/2" \
  "" \
  "[www]" \
  "user = appuser" \
  "group = appgroup" \
  "listen = 0.0.0.0:9000" \
  "pm = dynamic" \
  "pm.max_children = 10" \
  "pm.start_servers = 2" \
  "pm.min_spare_servers = 1" \
  "pm.max_spare_servers = 3" \
  "catch_workers_output = yes" \
  "clear_env = no" \
  > /opt/php-8.5.2/etc/php-fpm.conf

WORKDIR /app
USER appuser

EXPOSE 9000

# DEV: deixa fácil rodar comandos (composer, phpunit etc) + mantém php-fpm no ar
CMD ["php-fpm", "-y", "/opt/php-8.5.2/etc/php-fpm.conf"]
