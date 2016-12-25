#!/usr/bin/env bash
__DIR__=$(pwd)
# Install Drupal site.
docker-compose exec --user 82 php drush site-install\
    standard \
    --db-url="mysql://drupal:drupal@mariadb:3306/drupal"\
    --account-name=admin\
    --account-pass=admin\
    --account-mail=admin@eaxample.com\
    -y