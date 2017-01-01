# How to set up the project step by step.
## Requirements.
* Installed docker, docker-compose, web-browser.
* Tested on Mac, Linux.
## Process.
* Сlone the repository.
```
git clone https://github.com/vlikin/test-task-daxx-drupal-8.git
```
* Check that `master` branch is selected.
* Run install script.
```
sh ./docker-compose-script.sh
```
It ups containers, installs fresh Drupal site, applies project configuration,
runs tests, imports initial content.
* Open a web browser.
* Navigate to http://localhost:8000
* The project will be opened.
* Test it.beer

# Skills that are shown at the project.
* Docker Virtualizaion
* PHP, Nginx, MariaDB technology stack.
* Drupal 8.
* Git core.
* Out of the box project.
* Migration process.
* Blocks, Layouts, Custom blocks.
* Page manager, Panels, Panalizer.
* Views, contextual filters.
* FAPI, Ajax forms.
* Responsive image.



# Tips
## How to ...
## look at the migration status.
 
## How to update Drupal 8 core.
```
git subtree pull --prefix drupal dorg-drupal 8.3.x
```
## How to run a migration.
```
docker-compose exec --user 82 php ./drush migrate-import portfolio
```
## How to revert configurations.
```
docker-compose exec --user 82 php drush features-import features_master 
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