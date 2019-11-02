"use strict";

const gulp = require('gulp');
const nittro = require('gulp-nittro');
const scripts = require('./scripts');
const styles = require('./styles');

const builder = new nittro.Builder({
    vendor: {
        js: [
            'node_modules/jquery/dist/jquery.slim.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'
        ],
        css: [
            'node_modules/bootstrap/dist/css/bootstrap.min.css'
        ]
    },
    base: {
        core: true,
        datetime: true,
        neon: true,
        di: true,
        ajax: true,
        forms: false,
        page: true,
        flashes: true,
        routing: false
    },
    extras: {
        checklist: true,
        dialogs: true,
        confirm: true,
        dropzone: true,
        paginator: false,
        keymap: true,
        storage: false
    },
    libraries: {
        js: [
            'src/assets/js/Forms/BootstrapErrorRenderer.js',
            'src/assets/js/ClassSwitcher.js',
            'src/AdminModule/assets/js/scripts.js'
        ],
        css: [
            'src/assets/css/bootstrap-bridge.less',
            'src/AdminModule/assets/css/styles.less'
        ]
    },
    bootstrap: {
        services: {
            formErrorRenderer: 'App.Forms.BootstrapErrorRenderer()',
            classSwitcher: 'App.ClassSwitcher()!'
        }
    },
    stack: true
});

exports.js = function adminJs() {
    return scripts(builder, 'admin.min.js');
};

exports.css = function adminCss() {
    return styles(builder, 'admin.min.css');
};

exports.fonts = function adminFonts() {
    return gulp.src([
        'src/AdminModule/assets/fonts/*'
    ]).pipe(gulp.dest('public/fonts'));
};

exports['watch:js'] = function watchAdminJs() {
    return gulp.watch([
        'src/AdminModule/assets/js/**',
        'src/assets/js/**'
    ], exports.js);
};
exports['watch:css'] = function watchAdminCss() {
    return gulp.watch([
        'src/AdminModule/assets/css/**',
        'src/assets/css/**'
    ], exports.css);
};

exports.default = gulp.parallel(exports.js, exports.css, exports.fonts);
exports.watch = gulp.parallel(exports['watch:js'], exports['watch:css']);
