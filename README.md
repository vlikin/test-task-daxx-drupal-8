* [Task description](./docs/task-description.md)
* [Development tips](./docs/development-tips.md)

# How to set up the project step by step.

## Requirements.
* Installed docker, docker-compose, web-browser.
* Tested on Mac, Linux.

## Process.
* Сlone the repository.
```
git clone https://github.com/vlikin/test-task-daxx-drupal-8.git
```
* Move to the project.
* Check that `master` branch is selected.
* Up containers
```
docker-compose up -d
```
* Wait 2 minutes.
* Run install script.
```
sh ./docker-compose-script.sh
```
It ups containers, installs fresh Drupal site, applies project configuration,
runs tests, imports initial content.
* Open a web browser.
* Navigate to http://localhost:8000
* The project will be opened.
* Test it.
 * Select a type.
 * Year will be loaded.
 * Select a year.
 * Push the button `Submit`
 * The view is filtered according to filters.
 * Check that responsive images are used.
* Follow on /user
* Login as `admin` and password `admin`.
* Look from inside.

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