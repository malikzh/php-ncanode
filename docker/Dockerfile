FROM php:7.4-fpm

USER root

ARG uid

# Конфигурации
COPY conf/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN useradd -u 9944 -G www-data fpm-user

COPY ./entrypoint.sh /
RUN ["chmod", "+x", "/entrypoint.sh"]

WORKDIR /var/www/html

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
