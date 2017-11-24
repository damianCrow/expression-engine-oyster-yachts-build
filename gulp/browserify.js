import path from 'path'
import glob from 'glob'
import browserify from 'browserify'
import watchify from 'watchify'
import envify from 'envify'
import babelify from 'babelify'
import _ from 'lodash'
import vsource from 'vinyl-source-stream'
import buffer from 'vinyl-buffer'
import gulpif from 'gulp-if'

export default (gulp, plugins, args, config, taskTarget, browserSync) => {
  const dirs = config.directories
  const entries = config.entries

  const browserifyTask = (files) => {
    return files.map((entry) => {
      const dest = path.resolve(taskTarget)

      // Options
      const customOpts = {
        entries: [entry],
        debug: true,
        transform: [
          babelify, // Enable ES6 features
          envify // Sets NODE_ENV for better optimization of npm packages
        ]
      }

      let bundler = browserify(customOpts).transform('babelify', { presets: ['es2015'], plugins: ['add-module-exports'] })

      if (!args.production) {
        // Setup Watchify for faster builds
        const opts = _.assign({}, watchify.args, customOpts)
        bundler = watchify(browserify(opts).transform('babelify', { presets: ['es2015'], plugins: ['add-module-exports'] }))
      }

      const rebundle = () => {
        const startTime = new Date().getTime()
        bundler.bundle()
          .on('error', (err) => {
            plugins.util.log(
              plugins.util.colors.red('Browserify compile error:'),
              '\n',
              err.stack,
              '\n'
            )
            this.emit('end')
          })
          .on('error', plugins.notify.onError(config.defaultNotification))
          .pipe(vsource(entry))
          .pipe(buffer())
          .pipe(plugins.sourcemaps.init({ loadMaps: true }))
          .pipe(gulpif(args.production, plugins.uglify({ compress: {
            drop_debugger: false, unused: false, dead_code: false, comparisons: false
          }})))
          .on('error', plugins.notify.onError(config.defaultNotification))
          .pipe(plugins.rename((filepath) => {
            // Remove 'source' directory as well as prefixed folder underscores
            // Ex: 'src/_scripts' --> '/scripts'
            filepath.dirname = filepath.dirname.replace(dirs.source, '').replace('_', '')
          }))
          .pipe(plugins.sourcemaps.write('./'))
          .pipe(gulp.dest(dest))
          // Show which file was bundled and how long it took
          .on('end', () => {
            const time = (new Date().getTime() - startTime) / 1000
            console.log(
              plugins.util.colors.cyan(entry)
              + ' was browserified: '
              + plugins.util.colors.magenta(time + 's'))
            // return browserSync.reload('*.js')
          })
      }

      if (!args.production) {
        bundler.on('update', rebundle) // on any dep update, runs the bundler
        bundler.on('log', plugins.util.log) // output build logs to terminal
      }
      return rebundle()
    })
  }

  // Browserify Task
  gulp.task('browserify', (done) => {
    return glob('./' + path.join(dirs.source, dirs.scripts, entries.js), (err, files) => {
      if (err) {
        done(err)
      }

      return browserifyTask(files)
    })
  })
}
