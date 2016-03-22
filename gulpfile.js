'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var sassPaths = [
  'source/sass/libraries/foundation/foundation_source',
  'source/sass/libraries/foundation/motion_ui'
];

gulp.task('css', function () {
  gulp.src('./source/sass/style.scss')
    .pipe(sass({
      includePaths: sassPaths
    }).on('error', sass.logError))
    .pipe(sourcemaps.init())
    .pipe(cssnano())
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('./dist'));
});

gulp.task('scripts', function() {
  gulp.src(['./source/js/libraries/jquery.min.js', 
            './source/js/libraries/jquery.cycle2.min.js', 
            './source/js/libraries/foundation/foundation.core.js',
            './source/js/libraries/foundation/foundation.util.mediaQuery.js',
            './source/js/libraries/lightgallery/lightgallery.js',
            './source/js/libraries/lightgallery/lg-thumbnail.js',
            './source/js/libraries/select2.full.min.js',
            //'./source/js/libraries/lightgallery/lg-fullscreen.js',
            './source/js/app.js'])
    .pipe(concat('scripts.js'))
    .pipe(sourcemaps.write())
    .pipe(uglify())
    .pipe(gulp.dest('./dist'))
});

gulp.task('default', ['css', 'scripts']);

gulp.task('watch', function () {
  gulp.watch('./source/sass/**/*.scss', ['css']);
  gulp.watch('./source/js/**/**/*.js', ['scripts']);
});