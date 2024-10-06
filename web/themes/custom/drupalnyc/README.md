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

The files and directories of this theme are meant to use components as the base philosophy for building websites.

All the build tools are set up in a way so that developers working on this project are utilizing the same global assets
and versions, file compilation is easy to get started with and compilation itself is fast and efficient.

## Requirements

To use this theme you need to install & enable these modules in advance, if they are not you are likely to run into a white screen of death (WOD). Eventually this project will rely on Single Directory Components (SDC) within Drupal core, initial release 10.1.x, however we currently use the SDC module.

- Single Directory Components (Either the 1.x or 2.x branch works)
- Twig Field Value (2.x)
- Twig Tweak (3.x)

This theme and its build tools require the following local development tools:

- [Node.js](https://nodejs.org/en/)
- [NPM](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm)
- [NVM](https://github.com/nvm-sh/nvm/blob/master/README.md)

## Installation

1. Before installing any node modules, first be sure you are using the defined version of Node for this project. The following command requires the tool NVM Node package manager. If you do not have the recommended version of Node installed on your computer, NVM will prompt you to download and install the correct version -- `nvm use`
2. Once you are using the recommended version of Node, you may install all Node modules defined in the package.json file -- `npm install`
3. To correctly utilize Browsersync, copy the `.env-example` file and rename it `.env`. Within this file specify both your local website address and default browser.

## Use / Commands

**Local Development**

In order to watch file changes and compile files during local development, input the following command and allow both
Gulp and Webpack to watch for changes and/or additions to the libraries directory. -- `npm run watch`

**Production**

When local development is finished and ready to be pushed to a remote environment, build and minify all files, readying
all assets for production. In order to minify and optimize all files and assets, run the following command.
-- `npm run build`

## Configuration

**Browser Support:** This theme uses PostCSS and Babel to provide cross-browser support for newer CSS and JS features. Both of these tools rely upon the following files for configuration and settings.

- `postcss.config.js` - This file contains all settings, overrides and extra plugins for the PostCSS module, including PostCSS Preset Env and PostCSS Inline SVG, as detailed below.

- `babel.config.js` - This file contains all settings, overrides and extra plugins for the Babel module, as well as settings for caching JavaScript in Babel.

- `.browserslistrc` - This file contains instructions to other modules like Babel and PostCSS on what browsers to support when transpiling frontend assets. Currently, Drupalnyc is supporting the last 4 versions (`last 4 versions`) of any browser that more than 0.5% of the population is using (`> .5%`) and still supported (`not dead`).

To update your browserlist usage database: `npx update-browserslist-db@latest`

To see which browsers your build is supporting:

```
npx browserslist
```

**PostCSS Inline SVG**
Drupalnyc now offers an easy way to inject inline SVG's into your CSS. In order to take advantage of the PostCSS Inline SVG plugin, simple utilize the predefined function from this plugin, with path to the SVG and any inline styles that you need to add.

- Example: `background: svg-load('image.svg', fill=#000000);`

**Important**
The new PostCSS Inline SVG replaces the str.scss and svg.scss functionalities. Since this is a breaking change, all references to the past functionalities with inline SVGs will need to be updated to use the new inline SVG policy.

**Resources**

- [PostCSS](https://github.com/postcss/postcss)
- [PostCSS Preset Env](https://github.com/csstools/postcss-plugins/tree/main/plugin-packs/postcss-preset-env)
- [PostCSS Inline SVG](https://github.com/TrySound/postcss-inline-svg)
- [Babel](https://babeljs.io/)

## SASS Tools

There are several useful SASS functions and mixins available to developers when theming. All the documentation can be found in the respective files, but below is a list of available functions and mixins with examples and where to find them in the partials directory.

It is recommended to use modern SASS standards for importing partials with name-spacing, however, older standards are still available for importing partials.

`@use '../partials/partials' as *` instead of `@import '../partials/partials'`\
`@use '../partials/mixins/breakpoints' as breakpoints` instead of `@import '../partials/mixins/breakpoints'`

**Background Image**\
`partials/mixins/_media.scss`

Example Usage = `@include background-image(center);`

**Breakpoints**\
`partials/functions/_breakpoints.scss`\
`partials/mixins/_breakpoints.scss`\
`partials/settings/_breakpoints.scss`

Example Usage = `@include bp-min(tablet) {}`\
Example Usage = `@include bp-max(desktop) {}`\
Example Usage = `@include bp-between(tablet, desktop) {}`

**Calculations**\
`partials/functions/_calculations.scss`

Example Usage = `width: percent(250, 1000);`\
Example Usage = `font-size: rem(24px);`

**Color**\
`partials/settings/_colors.scss`

Example Usage = `background-color: var(--color-primary);`

**Flex Grid**\
`partials/mixins/_layout.scss`

Example Usage = `@include flex-grid(3, 'li', 40px, 40px);`

**Full Width**\
`partials/mixins/_layout.scss`

Example Usage = `@include full-width();`

**Heading Sizes**\
`partials/mixins/_typography.scss`

Example Usage = `@include font-h2()`

**Responsive IFrame**\
`partials/mixins/_media.scss`

Example Usage = `@include responsive-iframe(16, 9);`

**Transitions**\
`partials/settings/_animations.scss`\
`partials/functions/_animations.scss`

Example Usage = `transition: transition(all, 0.6s, easeInOutQuad)`

**Z-Index**\
`partials/settings/_layout.scss`\
`partials/functions/_layout.scss`

Example Usage = `z-index: z-index(bottomless-pit);`

## Typography

Font size and line height values are based on a typographic scale in order to provide consistent control of type sizes and vertical rythym across screen sizes. This scale can be found in `./libraries/global/settings/typography.scss` defined as `$typographic-scale`. See that file for more information on how the scale is structured and manipulated.

The `$typographic-scale` is converted into a set of font-size and line-height custom properties in the form of `--{prefix}-fs-{category}-{target-value}` and `--{prefix}-lh-{category}-{target-value}`. The resulting font-size is set in rem units and a default base font-size of 16px. The line-heights are set as unitless values.

While it's possible to use these custom properties directly in special cases, it's recommended to use the `type-scale` mixin to ensure appropriate line-heights are set for the corresponding font-sizes. This will help maintain design consistency throughout the project.

## Menu Structure

Since version 4.x, Drupalnyc ships with an accessible menu that works with Drupal's default menu system out of the box. To utilize for the main menu there are a few steps you will need to take:

By default the Main navigation menu provided by Drupal is used by Drupalnyc. To add links simply navigate to structure -> menus & select the Main navigation menu.

Add links by following the prompts provided for the fields. To add buttons you will need to type `<button>` within the menu link field rather than a link.

This structure is intentional, buttons provide one action which is to open a menu and not act as a link. This menu structure supports multiple levels of menus with submenu buttons, so you can build out the menu using the standard Drupal structure.

Drupalnyc takes the `menu--main.html.twig` file in `templates/menu` directory and passes it to the `menu--navigation` component which is located in the `components` directory. The `menu-navigation` component is built on the default Drupal menu strucutre and supports multiple levels of menus, submenu, and buttons as well as mobile menu functionality.

There is a base `menu` component that can be used to make any menu component accessible, an example might be adding a utility nav or sidebar navigation. That component does not support a mobile toggle format by default. If you need to add a mobile toggle to a menu component you can use the `menu--navigation` component as a base and add the mobile toggle functionality to your component.

## Third Party Libraries
Any third party libraries, _(Ex. GSAP or Splide.js)_ that are used in this project and not installed via npm or meant to be bundled this project's source code, should be placed in the /vendor directory and defined as an independent library the libraries.yml file and added as a dependency to the appropriate component or library definitions.

## Maintainers

- Jennifer Dust - jennifer@aten.io
- Philip Stier - philip@aten.io
- Bryon Urbanec - bryon@aten.io
- John Ferris - john@aten.io

## Contributing

All contributions must have an [corresponding issue](https://www.drupal.org/project/issues/drupalnyc?categories=All) and be made through merge requests.

This theme leverages Drupal.org's [Tugboat QA integration](https://www.drupal.org/docs/develop/git/using-git-to-contribute-to-drupal/using-live-previews-on-drupal-core-and-contrib-merge-requests) to ensure that all code is reviewed and tested before merging. When a merge request is opened, Tugboat will automatically create a preview environment for the merge request. This environment will be available for 5 days. If a preview environment needs to be recreated after that period, the merge request will need to be closed and reopened.

The preview environment has both the [Styleguide](https://www.drupal.org/project/styleguide) and [Storybook](https://www.drupal.org/project/storybook) modules enabled for previewing components. Each of these can be viewed at `/admin/appearance/styleguide` and `/storybook` respectively.