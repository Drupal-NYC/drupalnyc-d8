## Contents of this file

- About
- Requirements
- Installation
- Use / Commands
- Configuration
- SASS Tools
- Menu Structure
- Maintainers
- Contributing


## About

This directory contains Cypress end-to-end (e2e) tests for the Drupalnyc theme. There is a single spec file in `cypress/e2e/drupalnyc/drupalnyc.cy.js`. It is recommended to add additional tests to this file as it includes common setup and teardown logic that will generate and enable a new theme that further tests can be run against.

## Requirements

These tests run using the Cypress testing framework. To run these tests, you will need to have the following installed:

- [Node.js](https://nodejs.org/en/)
- [NPM](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm)
- [NVM](https://github.com/nvm-sh/nvm/blob/master/README.md)

## Installation

1. Before installing any node modules, first be sure you are using the defined version of Node for this project. The following command requires the tool NVM Node package manager. If you do not have the recommended version of Node installed on your computer, NVM will prompt you to download and install the correct version -- `nvm use`
2. Once you are using the recommended version of Node, you may install all Node modules defined in the package.json file -- `npm install`
3. Copy the `cypress.config.js.dist` file to `cypress.config.js` and update the `baseUrl` and `drushCommand` options to match your local environment.

## Use / Commands

These tests will automatically run when merge requests are created as part of Drupalnyc's Tugboat integration on Drupal.org. However, you will need to run cypress locally in order to test changes before pushing them to the repository.

To run the tests locally to ensure you haven't introduced any regressions, run the following command fomr this directory – replacing the `baseUrl` and `drushCommand` options with the appropriate values for your local environment:

```
npm run cy:run -- --spec ./cypress/e2e/drupalnyc/drupalnyc.cy.js
```

During development, you can run the tests in interactive mode using the following command:

```
npm run cy:open -- --config baseUrl=http://drupalnyc.lndo.site --env drushCommand="lando drush \$COMMAND"
```

Additionally, you can override config options from the command line.

```
npm run cy:run -- --spec ./cypress/e2e/drupalnyc/drupalnyc.cy.js --config baseUrl=http://drupalnyc.lndo.site --env drushCommand="lando drush \$COMMAND"
```