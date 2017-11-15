// Gulp
var gulp = require('gulp');

// Sass/CSS stuff
var sass = require('gulp-sass');
var cleanCSS = require('gulp-clean-css');

// scripts
var uglify = require('gulp-uglify');

// Utilities
var bower = require('gulp-bower');
var fs = require('fs');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
 
gulp.task('bower', function() {
  return bower({ cmd: 'update'});
});

gulp.task('copydeps', ['bower'], function() {
  gulp.src(['bower_components/featherlight/src/featherlight.css','bower_components/featherlight/src/featherlight.gallery.css'])
    .pipe(gulp.dest('./scss/vendor/'));
  gulp.src('bower_components/include-media/dist/_include-media.scss')
    .pipe(gulp.dest('./scss/util/'));
  gulp.src(['bower_components/featherlight/src/featherlight.min.js','bower_components/featherlight/src/featherlight.gallery.min.js','bower_components/jquery-detect-swipe/jquery.detect_swipe.js'])
    .pipe(gulp.dest('./js/vendor/'));
});

// Compile Sass
gulp.task('sass', function() {
  return gulp.src(['scss/toolkit-shortcodes.scss', 'scss/toolkit-shortcodes-admin.scss', 'scss/toolkit-gallery.scss'])
    .pipe(sass({
      includePaths: ['./scss'],
      outputStyle: 'expanded'
    }))
    .pipe(cleanCSS())
    .pipe(gulp.dest('./css/'));
});

gulp.task('scripts', ['minifyjs'], function() {
  return gulp.src(['./js/vendor/jquery.detect_swipe.min.js', './js/vendor/featherlight.min.js', './js/vendor/featherlight.gallery.min.js'])
    .pipe(concat('toolkit-gallery.js'))
    .pipe(gulp.dest('./js/'));
});

gulp.task('minifyjs', function(){
    return gulp.src('./js/vendor/jquery.detect_swipe.js')
        .pipe(rename('jquery.detect_swipe.min.js'))
        .pipe(uglify({output:{comments:/^\*/}}))
        .pipe(gulp.dest('./js/vendor/'));
});

// Watch Files For Changes
gulp.task('watch', function() {
  gulp.watch('scss/**/*.scss', ['sass']);
});

// Default Task
gulp.task('default', ['copydeps', 'sass', 'scripts', 'watch']);