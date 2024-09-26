FROM uselagoon/php-8.3-cli-drupal:latest

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
