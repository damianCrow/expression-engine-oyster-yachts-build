import path from 'path'

export default function (gulp, plugins, args, config, taskTarget) {
  const dirs = config.directories
  const dest = path.join(taskTarget, dirs.images.replace(/^_/, ''), '/sizes')

  // const imgageDefault = {

  // }

  const sizeConfigs = (format, extname) => [{
    width: 40,
    qulaity: 30,
    rename: {
      suffix: '-holder',
      extname,
    },
    format,
  },
  {
    width: '25%',
    qulaity: 85,
    rename: {
      suffix: '-x05',
      extname,
    },
    format,
  }, {
    width: '37.5%',
    qulaity: 85,
    rename: {
      suffix: '-x075',
      extname,
    },
    format,
  }, {
    width: '50%',
    qulaity: 85,
    rename: {
      suffix: '-x1',
      extname,
    },
    format,
  }, {
    width: '80%',
    qulaity: 75,
    rename: {
      suffix: '-x13',
      extname,
    },
    format,
  }, {
    width: '100%',
    qulaity: 70,
    rename: {
      suffix: '-x2',
      extname,
    },
    format,
  }]


  gulp.task('responsiveImages', () => (
    gulp.src(path.join(dirs.source, dirs.images, 'largest-orginal-size/**/*.{jpg,jpeg,png}'))
      // .pipe(plugins.changed(dest))
      .pipe(plugins.responsive([...sizeConfigs('jpeg', '.jpg'), ...sizeConfigs('webp', '.webp')], {
        stats: true,
        name: '*',
        errorOnEnlargement: false,
        errorOnUnusedConfig: false,
        errorOnUnusedImage: false,
        silent: true,
      }))
      .pipe(gulp.dest(dest))
  ))
}
