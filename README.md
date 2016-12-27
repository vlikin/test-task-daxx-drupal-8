## How to set up the project step by step.
* Сlone the repository.
```
git clone https://github.com/vlikin/test-task-daxx-drupal-8.git
```
* Check that `master` branch is selected.
* Up docker containers
```
docker-compose up -d
```

## How to update Drupal 8 core.
```
git subtree pull --prefix drupal dorg-drupal 8.3.x
```
**Note that the subtree must be added before the command.** Look at the way how it was added.

## How to import predefined configuration.
```
docker-compose exec --user 82 php drush config-import --partial --source=/drupal-initial-config/sync-short
```

## How to update initial config from the existed project. 
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

## Docker usefull comands for the project.
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