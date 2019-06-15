FROM php:alpine

LABEL Description="Database anonymisation application. Takes either a JSON, YAML, or PHP configuration file." Vendor="Elliot J. Reed" Version="1.0"

ENV PATH="/app/bin:${PATH}"

WORKDIR /app

COPY ./bin/anonymise.php /app/bin/anonymise
COPY ./src /app/src
COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock

RUN apk add --update icu yaml git openssh-client && \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        zlib-dev \
        bzip2-dev \
        libzip-dev \
        sqlite-dev \
        icu-dev \
        yaml-dev && \
    docker-php-ext-install bcmath pdo_mysql pdo_sqlite zip && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    pecl install yaml && \
    docker-php-ext-enable yaml && \
    apk del .build-deps && \
    { find /usr/local/lib -type f -print0 | xargs -0r strip --strip-all -p 2>/dev/null || true; } && \
    rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apk/* && \
    addgroup -S anonymiser && adduser -S -G anonymiser anonymiser && \
    chown -R anonymiser:anonymiser /app && \
    chmod -R 777 /app

USER anonymiser

RUN cd /app && \
    curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/app --filename=composer && \
    chmod +x /app/composer /app/bin/anonymise && \
    /app/composer install --no-progress --no-interaction --classmap-authoritative --no-suggest --no-dev && \
    rm -f /app/composer /app/composer.json /app/composer.lock && \
    cd /app/vendor && \
    find . -type f \( -iname "*readme*" ! -iname "*.php" \) -exec rm -vf {} + && \
    find . -type f \( -iname "*changelog*" ! -iname "*.php" \) -exec rm -vf {} + && \
    find . -type f \( -iname "*license*" ! -iname "*.php" \) -exec rm -vf {} +

VOLUME ["/app"]
ENTRYPOINT ["php"]
CMD ["/app/bin/anonymise"]
