const THEME_DIR = '../../../../custom/cypresstest';

describe('Drupalnyc theme generator', () => {

  beforeEach(() => {
  });

  before(() => {
    // Remove the theme from previous tests.
    cy.exec(`rm -rf ${THEME_DIR}`);
    // Ensure the generated theme does not exist.
    cy.readFile(`${THEME_DIR}/cypresstest.info.yml`).should('not.exist')

    cy.drush('config:set system.theme default olivero').then((result) => {
      cy.drush('cr');
    });

    // Generate a new theme.
    cy.exec('cd ../../ && php generator.php --name cypresstest --display-name "Cypress Test" --path themes\/custom --abbreviated cyptst', {
      env: {},
      timeout: 120000,
    }).then((result) => {
      // Ensure the generated theme was created.
      cy.readFile(`${THEME_DIR}/cypresstest.info.yml`)
        .should('contain', "name: 'Cypress Test'");
      cy.readFile(`${THEME_DIR}/libraries/partials/settings/_base.scss`)
        .should('contain', "$property-prefix: 'cyptst';");

      // Ensure drupalnyc specifc folders were removed from the generated theme.
      cy.readFile(`${THEME_DIR}/.tugboat/config.yml`)
        .should('not.exist')
      cy.readFile(`${THEME_DIR}/tests/cypress/package.json`)
        .should('not.exist')

      // Install npm dependencies.
      cy.exec(`cd ${THEME_DIR}/ && npm install`, {
        timeout: 120000,
      });

      // Enable the test theme.
      cy.drush('theme:install cypresstest').then((result) => {
        cy.drush('config:set system.theme default cypresstest');
        cy.drush('cr');
      });
    });
  });

  after(() => {
  });

  it('successfully runs gulp commands', () => {
    // Clean compiled assets.
    cy.exec(`cd ${THEME_DIR}/ && npm run clean`, {
      timeout: 120000,
    }).then((result) => {
      cy.readFile(`${THEME_DIR}/dist/css/global/wysiwyg.css`).should('not.exist')
      cy.readFile(`${THEME_DIR}/dist/js/global/global.js`).should('not.exist')
      cy.readFile(`${THEME_DIR}/components/accordion/accordion.css`).should('not.exist')
      cy.readFile(`${THEME_DIR}/components/accordion/accordion.js`).should('not.exist')

      // Rebuild the theme.
      cy.exec(`cd ${THEME_DIR}/ && npm run build`, {
        timeout: 120000,
      });
      cy.readFile(`${THEME_DIR}/dist/css/global/wysiwyg.css`).should('exist')
      cy.readFile(`${THEME_DIR}/dist/js/global/global.js`).should('exist')
      cy.readFile(`${THEME_DIR}/components/accordion/accordion.css`).should('exist')
      cy.readFile(`${THEME_DIR}/components/accordion/accordion.js`).should('exist')
    });
  });

  it('successfully enables the generated theme', () => {
    // Visit the homepage.
    cy.visit('/');
    // Ensure the Cypress Test theme is active by checking for the main content block.
    cy.get('#block-cypresstest-main-menu').should('exist');
  });
});
