const fs             = require('fs');
const gulp           = require('gulp');
const autoprefixer   = require('gulp-autoprefixer');
const cleanCSS       = require('gulp-clean-css');
const flatmap        = require('gulp-flatmap');
const include        = require('gulp-include');
const sass           = require('gulp-sass');
const uglify         = require('gulp-uglify');
const yaml           = require('js-yaml');
const pump           = require('pump');
const argv           = require('yargs').argv;

// read configuration
const CONFIG         = yaml.load(fs.readFileSync('configuration.yaml', 'utf8'));

// Build sass (scss) files
gulp.task('sass', function() {
    return gulp.src(CONFIG.paths.sass + '*.scss')
        .pipe(sass(CONFIG.sass)
            .on('error', sass.logError))
        .pipe(autoprefixer(CONFIG.autoprefixer))
        .pipe(cleanCSS(CONFIG.cleancss))
        .pipe(gulp.dest(CONFIG.outputPaths.css));
});

// Build js files
gulp.task('js', function(cb) {
    // read all js
    var stream = gulp.src(CONFIG.paths.js + '*.js')
        // loop over them to include required js
        .pipe(flatmap(function(stream, file) {
            // for each js file, call include
            return stream
                .pipe(include({
                    includePaths: [
                        CONFIG.paths.js
                    ]
                }))
                .on('error', console.log);
        }))

    // now call pump and give it the stream
    pump(
        stream,
        uglify(CONFIG.uglify),
        gulp.dest(CONFIG.outputPaths.js),
        cb
    );
});

/**
 * Build task: Calls all required sub tasks
 */
gulp.task('build', ['sass', 'js']);

/**
 * Watch task: Watches the files and executes the required tasks.
 */
gulp.task('watch', ['build'], function() {
    gulp.watch([CONFIG.paths.sass + '**/*.scss'], ['sass']);
    gulp.watch([CONFIG.paths.js + '**/*.js'], ['js']);
});

/**
 * Default task: Calls the build task
 */
gulp.task('default', ['build']);
