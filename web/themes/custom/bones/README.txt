INTRODUCTION

Bones - a Drupal 8 theme with all the bells & whistles, without the fluff.
We've kept the codebase as slim as possible to help you get your website up and
running as quickly as possible. This theme has been developed by Xequals for
the Drupal community. Be sure to check out the list of components at
https://bones.xequals.co.nz

REQUIREMENTS

Drupal core v8 and above

FEATURES

- Mobile first responsive approach
- Simple css framework out of the box
- Component based design
- Preconfigured Sass .scss files with base styles you can easily adapt to your
  grid, typography and colour scheme
- Gulp Sass compiler preconfigured with Browser-Sync, but you can easily use
  any other sass compiler.
- Default styles for Views including 2, 3, and 4 column responsive layouts
- Responsive Display Suite styling for the most common layouts
- Color Module integration
        - Quickly change basic colour settings such as page background and
          font colours
- Theme settings to easily enable/disable:
        - Fixed or regular heading style
        - Pop-out search button
        - Mobile menu with hamburger toggle

INSTALLATION & CONFIGURATION

1. Download the theme folder to your site repo at root/themes
2. Make a sub-theme from the starterkit folder
3. Enable the new theme

Compiling Sass using Gulp (Mac OSX)
Bones uses Gulp to compile sass, but you can use whatever preprocessor you like.
To run Gulp you must have the required node packages installed.

Bones uses the following packages:

- Gulp
- Gulp-sass
- Gulp-sourcemaps
- Gulp-autoprefixer
- Node-sass-globbing
- Gulp-plumber
- Gulp-cssmin
- Browser-sync
- Breakpoint-sass
- Compass-mixins

To setup a new theme to build with Gulp:

1. Open terminal
2. cd to the theme directory.
3. Run "npm install" or "yarn install" to install the required packages. 
4. Open the gulpfile.js and update the Browser-sync proxy location to your
   local development address (line 24).
5. Open the scss files and make your changes.
6. Run "npm run dev" or "yarn dev". 
7. Browser sync should automatically open a new browser tab. You will have to
   login to the site here to see changes appear.
8. Flush the cache and the new changes should appear automatically after each
   .scss file save.   


Bones on Drupal.org - https://www.drupal.org/project/bones_theme
