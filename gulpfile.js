

/*
 |-------------------------------------------------------
 | PLUGINS
 | TODO: Look into plugin module to reduce the amount of these requires
 |-------------------------------------------------------
 */

//
var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    autoprefixer = require('gulp-autoprefixer'),
    browserSync = require('browser-sync').create(),
    reload = browserSync.reload;

/*
 |-------------------------------------------------------
 | VARIABLES
 | TODO: Look into exporting to a module and using require('module name')
 |-------------------------------------------------------
 */

// Site proxy for browser-sync:
// http://local.dev/gulp_learning/public <-- This works too
var proxy = "localhost";

// Public Directory:
var publicDir = './public';

// Raw Assets:
var assetsDir = './assets';

// Bower Components:
var bowerDir = assetsDir + '/bower_components';

// Sass & Css directories and files:
var sassDir = assetsDir + '/sass';
var sassFile = sassDir + '/style.scss';
var cssOutput = publicDir + '/css';

// CSS libs & includes:


// Javascript directories and files:
var jsDir = assetsDir + '/js';
var jsFile = jsDir + 'app.js';
var jsOutputDir = publicDir + '/js';

// Javascript libraries & includes:



/*
 |-------------------------------------------------------
 | TASKS
 |-------------------------------------------------------
 */

 //Compiles the Sass file and outputs to the specified destination with autoprefixing
gulp.task('sass', function(){

    // Sass preprocessor config object
    var sassOptions = {
        errLogToConsole: true,
        outputStyle: 'compressed'
    };

    return gulp.src([sassFile])
        .pipe(concat('app.min.css'))
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(autoprefixer('last 2 versions'))
        .pipe(gulp.dest(cssOutput))
        .pipe(browserSync.stream())
        .pipe(notify({message: 'Successfully compiled Styles'}))
});

// Concat the specified JS files into one file
// Then uglify the concatenated file
// Then output to specified destination
gulp.task('scripts', function(){
    return gulp.src([jsFile])
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(jsOutputDir))
        .pipe(browserSync.stream())
        .pipe(notify({message: 'Successfully processed Javascript'}))
});


// Initialise browser-sync server:
gulp.task('browser-sync', function(){
    browserSync.init({
        proxy: proxy
    });
});

// Watch Task
// Wait for SRC changes then recompile and browser-sync
gulp.task('watch', function(){
    gulp.watch(sassDir + '/**/*.scss', ['sass']);
    gulp.watch(jsDir + '/**/*.js', ['scripts']);
    gulp.watch('./**/*.+(html|php|htm)').on('change', reload);
});

// Task that is run when gulp is invoked with no task name
gulp.task('default', ['sass', 'scripts', 'browser-sync','watch']);