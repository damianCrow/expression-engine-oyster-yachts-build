'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var sassPaths = [
  'source/sass/foundation/foundation_source',
  'source/sass/foundation/motion_ui'
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
  gulp.src(['./wp-content/themes/oyster/js/jquery.min.js', 
            './wp-content/themes/oyster/js/jquery.cycle2.min.js', 
            './wp-content/themes/oyster/js/jquery.magnific-popup.min.js', 
            './wp-content/themes/oyster/js/jquery.slimscroll.min.js', 
            './wp-content/themes/oyster/js/jquery.fullpage.js', 
            './wp-content/themes/oyster/js/flipclock.min.js',
            './wp-content/themes/oyster/js/panzoom.js',
            './wp-content/themes/oyster/js/jquery.mousewheel.js',
            './wp-content/themes/oyster/js/app.js'])
    .pipe(concat('scripts.js'))
    .pipe(sourcemaps.write())
    .pipe(uglify())
    .pipe(gulp.dest('./dist'))
});

gulp.task('default', ['css'/*, 'scripts'*/]);

gulp.task('watch', function () {
  gulp.watch('./source/sass/*.scss', ['css']);
  gulp.watch('./source/js/*.js', ['scripts']);
});