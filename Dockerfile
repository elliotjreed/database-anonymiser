FROM php:alpine

COPY ./vendor /app/vendor
COPY ./bin/anonymise.php /app/bin/anonymise
COPY ./src /app/src
COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock
COPY ./README.md /app/readme

