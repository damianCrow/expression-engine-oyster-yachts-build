import path from 'path'

export default function (gulp, plugins, args, config, taskTarget) {
  const dirs = config.directories
  const dest = path.join(taskTarget, dirs.images.replace(/^_/, ''), '/sizes')

  const imgageDefault = {
    name: '*',
    errorOnEnlargement: false,
    errorOnUnusedConfig: false,
    errorOnUnusedImage: false,
    silent: true,
  }

  gulp.task('responsiveImages', () => (
    gulp.src(path.join(dirs.source, dirs.images, 'largest-orginal-size/**/*.{jpg,jpeg,png}'))
      .pipe(plugins.changed(dest))
      .pipe(plugins.responsive({ stats: false }, [{
        ...imgageDefault,
        width: 40,
        qulaity: 30,
        rename: {
          suffix: '-holder',
        },
      },
      {
        ...imgageDefault,
        width: '25%',
        qulaity: 85,
        rename: {
          suffix: '-x05',
        },
      }, {
        ...imgageDefault,
        width: '37.5%',
        qulaity: 85,
        rename: {
          suffix: '-x075',
        },
      }, {
        ...imgageDefault,
        width: '50%',
        qulaity: 85,
        rename: {
          suffix: '-x1',
        },
      }, {
        ...imgageDefault,
        width: '80%',
        qulaity: 75,
        rename: {
          suffix: '-x13',
        },
      }, {
        ...imgageDefault,
        width: '100%',
        qulaity: 70,
        rename: {
          suffix: '-x2',
        },
      },
      ]))
      .pipe(gulp.dest(dest))
  ))
}
