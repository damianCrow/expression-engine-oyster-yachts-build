export default function (gulp, plugins, args, config, taskTarget, browserSync) {
  // BrowserSync
  gulp.task('browserSync', () => {
    browserSync.init({
      open: args.open ? 'local' : false,
      startPath: config.baseUrl,
      port: config.port || 3000,
      server: {
        baseDir: taskTarget,
        routes: (() => {
          const routes = {}

          // Map base URL to routes
          routes[config.baseUrl] = taskTarget

          return routes
        })(),
      },
    })
  })
}
