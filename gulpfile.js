var gulp = require('gulp');
var phpunit = require('gulp-phpunit');

gulp.task('phpunit', function() {
    var options = {
        debug: false
    };
    gulp.src('phpunit.xml.dist')
        .pipe(phpunit('./vendor/bin/phpunit',options));
});

gulp.task('tdd', function() {
    gulp.watch(['./src/**', './tests/**'], ['phpunit']);
});
