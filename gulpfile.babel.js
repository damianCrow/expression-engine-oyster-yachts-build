import browserSyncLib from 'browser-sync'
import glob from 'glob'
import gulp from 'gulp'
import gulpLoadPlugins from 'gulp-load-plugins'
import minimist from 'minimist'
import pjson from './package.json'


// Load all gulp plugins based on their names
// EX: gulp-copy -> copy
const plugins = gulpLoadPlugins()

const defaultNotification = (err) => {
  return {
    subtitle: err.plugin,
    message: err.message,
    sound: 'Funk',
    onLast: true,
  }
}

const config = Object.assign({}, pjson.config, defaultNotification)

const args = minimist(process.argv.slice(2))
const dirs = config.directories
const taskTarget = args.production ? dirs.destination : dirs.temporary

// Create a new browserSync instance
// const browserSync = browserSyncLib.create()

// This will grab all js in the `gulp` directory
// in order to load all gulp tasks
glob.sync('./gulp/**/*.js')
  .filter(file => (/\.(js)$/i)
    .test(file))
  .map((file) => {
    require(file)(gulp, plugins, args, config, taskTarget)
  })

// Default task
gulp.task('default', ['clean'], () => {
  gulp.start('build')
})

// Build production-ready code
gulp.task('build', [
  'copy',
  'responsiveImages',
  'imagemin',
  'fonts',
  'sass',
  'browserify',
])

// Server tasks with watch
gulp.task('serve', [
  'responsiveImages',
  'imagemin',
  'copy',
  'fonts',
  'sass',
  'browserify',
  // 'browserSync',
  'watch',
])

// Testing
gulp.task('test', ['eslint'])
