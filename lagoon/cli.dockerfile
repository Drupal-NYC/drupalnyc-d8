FROM uselagoon/php-8.3-cli-drupal:latest

COPY composer.* /app/
COPY assets /app/assets
RUN composer install --no-dev
COPY . /app
RUN mkdir -p -v -m775 /app/web/sites/default/files && chmod 775 /app/web/sites/default


# Define where the Drupal Root is located
ENV WEBROOT=web
