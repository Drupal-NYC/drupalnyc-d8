import 'cypress-iframe';

// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

/**
 * Returns a series of arguments, separated by spaces.
 *
 * @param {*} args
 * @returns
 */
function stringifyArguments(args) {
  return args.join(' ');
}

/**
 * Returns a string from an array of options.
 *
 * @param {array} options
 * @returns
 */
function stringifyOptions(options) {
  return Object.keys(options).map(option => {
    let output = `--${option}`;

    if (options[option] === true) {
      return output;
    }

    if (options[option] === false) {
      return '';
    }

    if (typeof options[option] === 'string') {
      output += `="${options[option]}"`;
    } else {
      output += `=${options[option]}`
    }

    return output;
  }).join(' ')
}

/**
 * Run a drush command.
 */
Cypress.Commands.add('drush', (command, args = [], options = {}) => {
  const drushCommand = `${command} ${stringifyArguments(args)} ${stringifyOptions(options)} -y`;
  const execCommand = Cypress.env('drushCommand').replace('$COMMAND', drushCommand);
  return cy.exec(execCommand);
});

/**
 * Logs out the user.
 */
Cypress.Commands.add('drupalLogout', () => {
  cy.visit('/user/logout');
})

/**
 * Basic user login command. Requires valid username and password.
 *
 * @param {string} username
 *   The username with which to log in.
 * @param {string} password
 *   The password for the user's account.
 */
Cypress.Commands.add('loginAs', (username, password) => {
  cy.drupalLogout();
  cy.visit('/user/login');
  cy.get('#edit-name')
    .type(username);
  cy.get('#edit-pass').type(password, {
    log: false,
  });
  cy.get('input#edit-submit').contains('Log in').click();
});

/**
 * Logs a user in by their uid via drush uli.
 */
Cypress.Commands.add('loginUserByUid', (uid) => {
  cy.drush('user-login', [], { uid, uri: Cypress.config('baseUrl') })
    .its('stdout')
    .then(function (url) {
      cy.visit(url);
    });
});

/**
 * Logs a user in by their username via drush uli.
 */
Cypress.Commands.add('loginUserByUsername', (username) => {
  cy.drush('user-login', [], { name: username, uri: Cypress.config('baseUrl') })
    .its('stdout')
    .then(function (url) {
      cy.visit(url);
    });
});

Cypress.Commands.add('enableLanguage', (langCode) => {
  cy.get('#edit-languages').then(($table) => {
    if ($table.find(`[data-drupal-selector="edit-languages-${langCode}"]`).length === 0) {
      cy.get('a').contains('Add language').click();
      cy.get('#edit-predefined-langcode').select(langCode);
      cy.get('#edit-predefined-submit').click();
      cy.wait('@addLanguage', { timeout: 70000 });
      cy.url().should('contain', 'admin/config/regional/language');
    }
  });
});

/**
 * Runs a series of basic ME tests (add layouts, edit them, etc.).
 */
Cypress.Commands.add('basicMercuryEditorInteractions', () => {
  cy.meAddComponent('me_test_section');
  cy.meChooseLayout('layout_twocol');
  cy.meSaveComponent().then((section) => {
    cy.meAddComponent('me_test_text', {
      region: 'first',
      section
    });
    cy.meSetCKEditor5Value('field_me_test_text', 'Left');
    cy.meSaveComponent().then((component) => {
      cy.iframe('#me-preview').find(component).should('contain', 'Left');
    });

    cy.meAddComponent('me_test_text', {
      region: 'second',
      section
    });
    cy.meSetCKEditor5Value('field_me_test_text', 'Right');
    cy.meSaveComponent().then((component) => {
      cy.iframe('#me-preview').find(component).should('contain', 'Right');
    });
  });

  cy.meSavePage();
  cy.meExitEditor();

  cy.meEditPage();
  cy.meFindComponent('Left').then((component) => {
    cy.meEditComponent(component);
    cy.meSetCKEditor5Value('field_me_test_text', 'Left - edited');
    cy.meSaveComponent().then((component) => {
      cy.iframe('#me-preview').find(component).should('contain', 'Left - edited');
    });
  });

  cy.meFindComponent('Right').then((component) => {
    cy.meEditComponent(component);
    cy.meSetCKEditor5Value('field_me_test_text', 'Right - edited');
    cy.meSaveComponent().then((component) => {
      cy.iframe('#me-preview').find(component).should('contain', 'Right - edited');
    });
  });

  cy.iframe('#me-preview').find('[data-type="me_test_section"]').first().then((section) => {
    cy.meAddComponent('me_test_section', { after: section });
    cy.meChooseLayout('layout_threecol_33_34_33');
    cy.meSaveComponent().then((section) => {

      cy.meAddComponent('me_test_text', {
        region: 'first',
        section
      });
      cy.meSetCKEditor5Value('field_me_test_text', 'Bottom left');
      cy.meSaveComponent().then((component) => {
        cy.iframe('#me-preview').find(component).should('contain', 'Bottom left');
      });

      cy.meAddComponent('me_test_text', {
        region: 'second',
        section
      });
      cy.meSetCKEditor5Value('field_me_test_text', 'Bottom center');
      cy.meSaveComponent().then((component) => {
        cy.iframe('#me-preview').find(component).should('contain', 'Bottom center');
      });;

      cy.meAddComponent('me_test_text', {
        region: 'third',
        section
      });
      cy.meSetCKEditor5Value('field_me_test_text', 'Bottom right');
      cy.meSaveComponent().then((component) => {
        cy.iframe('#me-preview').find(component).should('contain', 'Bottom right');
      });;
    });
    cy.meSavePage();
    cy.meExitEditor();
  });
});
