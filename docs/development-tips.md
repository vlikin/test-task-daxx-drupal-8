# Tips
## How to ...
## look at the migration status.

## generate content.
```
docker-compose exec --user 82 php ./drush generate-content 5 --types=portfolio
```
 
## update Drupal 8 core.
```
git subtree pull --prefix drupal dorg-drupal 8.3.x
```
## run a migration.
```
docker-compose exec --user 82 php ./drush migrate-import portfolio
```
## revert configurations.
```
docker-compose exec --user 82 php drush features-import features_master 
```
**Note that the subtree must be added before the command.** Look at the way how it was added.

## import predefined configuration.
```
docker-compose exec --user 82 php drush config-import --partial --source=/drupal-initial-config/sync-short
```

## update initial config from the existed project. 
```
docker-compose exec --user 82 php drush config-export
cp -R drupal/sites/default/files/config_F82RMqlbB3TxlLF2NAhJz5K-Tb-Y2m4mEcSZCMLpHj41sjHXHvTOZYGHgA5hssBYylifb6EnWw/sync drupal-initial-config
```

## The way how Drupal 8 core was merged to the project. 
```
git add remote dorg-drupal https://git.drupal.org/project/drupal.git
git fetch --depth=1 dorg-drupal
git subtree add --prefix drupal dorg-drupal 8.3.x
```

# Docker usefull comands for the project.
* Project's containers: `php`, `mariadb`, `nginx`, `mailhog`
* Start containers.
```
docker-compose start
```
* Stops containers.
```
docker-compose stop
```
* Gets an access to a container.
```
docker-compose exec mariadb sh
```
* Executes MySQL query and output results on the screen.
```
docker-compose exec mariadb sh -c "mysql -udrupal -pdrupal drupal -e 'show tables;'"
```
* Views logs of containers
```
docker-compose logs -f mariadb
```
* Runs composer.
```
docker-compose exec --user 82 php composer update
```
* Clear Drupal's cache.
```
docker-compose exec --user 82 php drush cache-rebuild
```