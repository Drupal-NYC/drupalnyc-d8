# DrupalNYC Drupal Site

[drupalnyc.org](https://www.drupalnyc.org/) is hosted on Amazee.io.

# Local Environment Using DDEV

[DDEV documentation](https://ddev.readthedocs.io/en/stable/)

## Initial Setup

1. Follow the documentation to install DDEV.
2. Create a directory to contain the site (e.g. `mkdir ~/Sites/drupalnyc`)
3. Change to that directory (e.g. `cd ~/Sites/drupalnyc`)
4. `git clone git@github.com:Drupal-NYC/drupalnyc-d8.git .`
5. `ddev start`
6. `terminus login`
7. `ddev robo rebuild:db drupalnyc.live`

You now have a fully functional local environment with the latest database and files from the Pantheon site's Live environment, accessible at [https://drupalnyc.lndo.site/](https://drupalnyc.lndo.site/)

## Useful Commands

`git push` as normal.

Drush: `ddev drush <command>`

Robo: `ddev robo list`

## Enable XDebug

`ddev xdebug on`

Now everything will run slower :)

## Lagoon

The lagoon hosting system uses Docker and Kubernettes.

- `docker-compose.yml` defines the services used to host the site.
- `.lagoon.yml` defines deployment tasks and environments.
- Service containers are built using the dockerfiles in the `lagoon` directory.
  Mostly these are adapted directly from Lagoon provided examples. the `cli.dockerfile` has been customized to build
  our theme after running a `composer install`.  This means that our deployed code is built into to container that is
  used to host the site.

It's possible to ssh into the running containers.  See [https://uselagoon.github.io/lagoon-cli/](https://uselagoon.github.io/lagoon-cli/).
