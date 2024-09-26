FROM uselagoon/php-8.3-cli-drupal:latest
FROM node:20.11.0-alpine AS node

COPY --from=node /usr/lib/* /usr/lib
COPY --from=node /usr/local/share/* /usr/local/share
COPY --from=node /usr/local/lib/* /usr/local/lib
COPY --from=node /usr/local/include/* /usr/local/include
COPY --from=node /usr/local/bin/* /usr/local/bin

COPY composer.* /app/
COPY assets /app/assets
COPY patches /app/patches

RUN composer install --no-dev

COPY . /app

RUN cd /app/web/themes/custom/drupalnyc \
    && npm install \
    && npm run build \
    && rm -rf ./node_modules

RUN mkdir -p -v -m775 /app/web/sites/default/files && chmod 775 /app/web/sites/default


# Define where the Drupal Root is located
ENV WEBROOT=web
