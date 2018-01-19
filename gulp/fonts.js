const path = require('path');

module.exports = function(gulp, plugins, args, config, taskTarget) {
  const dirs = config.directories;

  const dest = path.join(taskTarget, dirs.fonts.replace(/^_/, ''));

  gulp.task('fonts', () => {
    return gulp.src([path.join(dirs.source, dirs.fonts, '/**'),
      'node_modules/font-awesome/fonts/*']).pipe(gulp.dest(dest));
  });

  gulp.dest(file => path.join(build_dir, path.dirname(file.path)));

};
