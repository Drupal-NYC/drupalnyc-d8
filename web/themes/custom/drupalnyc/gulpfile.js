// Basic gulpfile for compiling SCSS to CSS
// Will inject changes as they happen via browser-sync
// For Gulp version 4 and above only
// See the README for more information

'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var importer = require('node-sass-globbing');
var plumber = require('gulp-plumber');
var browserSync = require('browser-sync').create();

var sass_config = {
  importer: importer,
  includePaths: [
     // These are the paths to the npm modules installed globally.
     // If you have installed the modules locally, or aren't using Mac OSX,
     // these paths will need to be adjusted.
    '/usr/local/lib/node_modules/breakpoint-sass/stylesheets/',
    '/usr/local/lib/node_modules/compass-mixins/lib/',
  ]
};

gulp.task('browser-sync', function(done) {
    browserSync.init({
        injectChanges: true,
        // Update the proxy location to the local site location to see
        // BrowserSync changes reload automatically
        // Do not include http
        proxy: "YOUR-LOCAL-SITE:PORT"
    });
    gulp.watch('./sass/*.scss', gulp.task(['sass']));
    gulp.watch(['./css/.styles.css']).on('change', browserSync.reload);
    done();
});

gulp.task('sass', function (done) {
  gulp.src('./sass/**/*.scss')
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(sass(sass_config).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: [
        'last 2 versions',
        'android 4',
        'opera 12'
      ]
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('./css'));
    done();
});

var build = gulp.series('browser-sync', ['sass']);

gulp.task('default', build);
