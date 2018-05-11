FROM alpine:latest
LABEL mantainer="Elliot J. Reed <docker-contact@elliotjreed.com>"

WORKDIR /tmp
COPY ./composer_installer.sh /tmp/composer_installer.sh
RUN apk --no-cache add bash ca-certificates curl git unzip \
        php7 php7-bcmath php7-ctype php7-curl php7-dom php7-exif php7-fileinfo php7-json php7-mbstring php7-tokenizer \
        php7-phar php7-simplexml php7-xdebug php7-xml php7-xmlreader php7-xmlwriter php7-zip php7-zlib \
        php7-pdo php7-pdo_mysql php7-pdo_pgsql php7-pdo_sqlite && \
    chmod 777 /tmp/composer_installer.sh && \
    sh /tmp/composer_installer.sh && \
    rm -f /tmp/composer_installer && \
    composer require phpunit/phpunit --no-interaction --no-suggest --no-progress && \
    composer require squizlabs/php_codesniffer --no-interaction --no-suggest --no-progress && \
    composer require php-coveralls/php-coveralls --no-interaction --no-suggest --no-progress && \
    ln -s /tmp/vendor/bin/phpunit /usr/local/bin/phpunit && \
    ln -s /tmp/vendor/bin/php-coveralls /usr/local/bin/php-coveralls && \
    ln -s /tmp/vendor/bin/phpcs /usr/local/bin/phpcs && \
    sed -i 's/\;z/z/g' /etc/php7/conf.d/xdebug.ini

VOLUME ["/app"]
WORKDIR /app

ENTRYPOINT ["/usr/local/bin/phpunit"]
CMD ["--version"]
