"use strict";

const gulp = require('gulp');
const nittro = require('gulp-nittro');
const scripts = require('./scripts');
const styles = require('./styles');

const builder = new nittro.Builder({
    vendor: {
        js: [],
        css: []
    },
    base: {
        core: true,
        datetime: true,
        neon: true,
        di: true,
        ajax: true,
        forms: false,
        page: true,
        flashes: false,
        routing: false
    },
    extras: {
        checklist: false,
        dialogs: false,
        confirm: false,
        dropzone: false,
        paginator: false,
        keymap: false,
        storage: false
    },
    libraries: {
        js: [
            'src/PublicModule/assets/js/scripts.js'
        ],
        css: [
            'src/PublicModule/assets/css/styles.less'
        ]
    },
    bootstrap: {},
    stack: false
});

exports.js = function publicJs() {
    return scripts(builder, 'public.min.js');
};

exports.css = function publicCss() {
    return styles(builder, 'public.min.css');
};

exports.fonts = function publicFonts() {
    return gulp.src([
        'src/PublicModule/assets/fonts/*'
    ]).pipe(gulp.dest('public/fonts'));
};

exports['watch:js'] = function watchPublicJs() {
    return gulp.watch([
        'src/PublicModule/assets/js/**',
        'src/assets/js/**'
    ], exports.js);
};
exports['watch:css'] = function watchPublicCss() {
    return gulp.watch([
        'src/PublicModule/assets/css/**',
        'src/assets/css/**'
    ], exports.css);
};

exports.default = gulp.parallel(exports.js, exports.css, exports.fonts);
exports.watch = gulp.parallel(exports['watch:js'], exports['watch:css']);
