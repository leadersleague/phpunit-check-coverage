##################################################################
# BUILDER staging
##################################################################
FROM registry.leadersleague.tv/php:v1 AS builder

RUN mkdir -p /home/www-data /root/.ssh

ARG SSH_KEY64
ENV SSH_KEY64=$SSH_KEY64
RUN echo $SSH_KEY64 > /tmp/ssh64

RUN base64 -d /tmp/ssh64 > /root/.ssh/id_rsa
RUN chmod 600 /root/.ssh/id_rsa
RUN base64 -d /tmp/ssh64 > /root/private_key
RUN chmod 600 /root/private_key
ENV SSH_PRIVATE_KEY=/root/private_key

RUN touch /root/.ssh/known_hosts
RUN ssh-keyscan -t rsa bitbucket.org > /root/.ssh/known_hosts

RUN mkdir -p /var/www
COPY . /var/www/.

WORKDIR /var/www

RUN composer install --no-scripts

CMD ["php-fpm"]

##################################################################
# TEST staging
##################################################################
FROM builder AS test

ENV XDEBUG_VERSION 2.5.4
RUN pecl install channel://pecl.php.net/xdebug-${XDEBUG_VERSION}

COPY docker/php.ini $PHP_INI_DIR/conf.d/php.ini
COPY docker/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini
COPY docker/php-fpm.d/www.conf $PHP_INI_DIR/../php-fpm.d/www.conf

COPY --from=builder /var/www /var/www
WORKDIR /var/www

RUN cp -rf /var/www/app/config/parameters.yml.ci /var/www/app/config/parameters.yml
RUN SYMFONY_ENV=test composer install --no-scripts
#SF 2.x
RUN php vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php || true
RUN rm -rf app/cache/* || true
RUN rm -rf app/logs/* || true

RUN /var/www/bin/phpunit -c /var/www/app/phpunit.xml.dist --coverage-html build/reports/phpunit/html --coverage-clover build/reports/phpunit/coverage.xml

LABEL image=test

##################################################################
#analysis
##################################################################
FROM openjdk:11-stretch AS runner-analysis

RUN mkdir -p /var/www
COPY --from=test /var/www /var/www
WORKDIR /var/www

# common
RUN apt-get update && apt-get install -y --no-install-recommends wget unzip && rm -r /var/lib/apt/lists/*

RUN wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.0.0.1744-linux.zip
RUN unzip sonar-scanner-cli-4.0.0.1744-linux.zip

ARG SONAR_KEY
ENV SONAR_KEY=$SONAR_KEY
ARG SONAR_URL
ENV SONAR_URL=$SONAR_URL
RUN ./sonar-scanner-4.0.0.1744-linux/bin/sonar-scanner -Dsonar.projectKey=$SONAR_KEY -Dsonar.host.url=$SONAR_URL

LABEL image=analysis

##################################################################
# RUNNER staging
##################################################################
FROM builder AS runner

RUN mkdir -p /var/www

COPY --from=builder /var/www /var/www

WORKDIR /var/www

## Clean test dependencies
RUN SYMFONY_ENV=prod composer install --no-interaction --no-progress --optimize-autoloader --prefer-dist --no-dev --no-scripts

#SF 2.x
RUN php vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php || true
RUN rm -rf app/cache/* || true
RUN rm -rf app/logs/* || true
#SF 3.x
RUN php vendor/sensio/distribution-bundle/Resources/bin/build_bootstrap.php || true
RUN rm -rf var/cache/* || true
RUN rm -rf var/logs/* || true

RUN rm -rf /var/www/app/config/parameters.yml

##################################################################
# PROD staging
##################################################################
FROM builder AS runner-prod

RUN mkdir -p /var/www/html /data
RUN chown -R www-data:www-data /data

USER www-data

COPY --chown=www-data:www-data --from=runner /var/www/app /data/app
COPY --chown=www-data:www-data --from=runner /var/www/src /data/src
COPY --chown=www-data:www-data --from=runner /var/www/bin /data/bin
COPY --chown=www-data:www-data --from=runner /var/www/composer.json /data/composer.json
COPY --chown=www-data:www-data --from=runner /var/www/composer.lock /data/composer.lock
COPY --chown=www-data:www-data --from=runner /var/www/vendor /data/vendor
COPY --chown=www-data:www-data --from=runner /var/www/var /data/var
COPY --chown=www-data:www-data --from=runner /var/www/web/app.php /data/web/app.php

USER root
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

CMD ["php-fpm"]
