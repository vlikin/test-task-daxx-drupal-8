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
* Test it.
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