FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install mysqli pdo_mysql pdo_pgsql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY conserje-smart-frontend/ /var/www/html/conserje_smart/conserje-smart-frontend/
COPY conserje-smart-backend/php/ /var/www/html/conserje_smart/conserje-smart-backend/php/
COPY conserje-smart-backend/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && printf 'ok\n' > /var/www/html/healthz

EXPOSE 10000

CMD ["/usr/local/bin/docker-entrypoint.sh"]
