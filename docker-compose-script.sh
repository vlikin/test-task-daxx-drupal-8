#!/usr/bin/env bash
__DIR__=$(pwd)
docker-compose up -d
docker-compose exec --user 82 php chmod u+w sites/default
docker-compose exec --user 82 php chmod u+w sites/default/settings.php
docker-compose exec --user 82 php rm -fR sites/default/settings.php sites/default/files

# Installs Drupal site.
docker-compose exec --user 82 php drush site-install\
    standard \
    --db-url="mysql://drupal:drupal@mariadb:3306/drupal"\
    --account-name=admin\
    --account-pass=admin\
    --account-mail=admin@eaxample.com\
    -y

# Applies project functionality.
# docker-compose exec --user 82 php drush config-import --partialy --source=/drupal-initial-config/sync-short -y
docker-compose exec --user 82 php drush en features_master -y

# Sets front page.
docker-compose exec --user 82 php drush config-set "system.site" page.front /portfolio

# Imports initial content.
docker-compose exec --user 82 php ./drush migrate-import images
docker-compose exec --user 82 php ./drush migrate-import portfolio

# Generates content.
docker-compose exec --user 82 php ./drush generate-content 5 --types=portfolio
