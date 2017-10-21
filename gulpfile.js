const gulp = require('gulp'),
    pump = require('pump'),
    filter = require('gulp-filter'),
    nittro = require('gulp-nittro'),
    uglify = require('gulp-uglify'),
    less = require('gulp-less'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    cssnano = require('cssnano'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat');


const publicBuilder = new nittro.Builder({
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




const adminBuilder = new nittro.Builder({
    vendor: {
        js: [
            'node_modules/jquery/dist/jquery.slim.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js'
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
            'src/assets/js/TabHelper.js',
            'src/assets/js/ClassToggle.js',
            'src/AdminModule/assets/js/scripts.js'
        ],
        css: [
            'src/AdminModule/assets/css/styles.less'
        ]
    },
    bootstrap: {
        services: {
            tabHelper: 'App.TabHelper()!',
            classToggle: 'App.ClassToggle()!'
        }
    },
    stack: false
});


function exclude(pattern, ...queue) {
    let f = filter(file => !pattern.test(file.path), {restore: true});
    queue.unshift(f);
    queue.push(f.restore);
    return queue;
}

function createTaskQueue(outputFile, builder) {
    let type = /\.js$/.test(outputFile) ? 'js' : 'css',
        queue = [
            nittro(type, builder),
            sourcemaps.init({loadMaps: true})
        ];

    if (type === 'js') {
        queue.push(... exclude(/\.min\.[^.]+$/,
            uglify({compress: true, mangle: false})
        ));
    } else {
        queue.push(... exclude(/\.min\.[^.]+$/,
            ... exclude(/\.css$/, less()),
            postcss([ autoprefixer(), cssnano() ])
        ));
    }

    queue.push(
        concat(outputFile),
        sourcemaps.write('.', {mapFile: (path) => path.replace(/\.[^.]+(?=\.map$)/, '')}),
        gulp.dest('public/' + type)
    );

    return queue;
}


gulp.task('public:js', function (cb) {
    pump(createTaskQueue('public.min.js', publicBuilder), cb);
});


gulp.task('public:css', function (cb) {
    pump(createTaskQueue('public.min.css', publicBuilder), cb);
});


gulp.task('public:fonts', function () {
    return gulp.src([
        'src/assets/fonts/*'
    ]).pipe(gulp.dest('public/fonts'));
});


gulp.task('admin:js', function (cb) {
    pump(createTaskQueue('admin.min.js', adminBuilder), cb);
});


gulp.task('admin:css', function (cb) {
    pump(createTaskQueue('admin.min.css', adminBuilder), cb);
});


gulp.task('admin:fonts', function () {
    return gulp.src([
        'node_modules/bootstrap/dist/fonts/*'
    ]).pipe(gulp.dest('public/fonts'))
});

gulp.task('watch:public:css', function () {
    return gulp.watch([
        'src/PublicModule/assets/css/**',
        'src/assets/css/**'
    ], [
        'public:css'
    ]);
});

gulp.task('watch:public:js', function () {
    return gulp.watch([
        'src/PublicModule/assets/js/**',
        'src/assets/js/**'
    ], [
        'public:js'
    ]);
});

gulp.task('watch:admin:css', function () {
    return gulp.watch([
        'src/AdminModule/assets/css/**',
        'src/assets/css/**'
    ], [
        'admin:css'
    ]);
});

gulp.task('watch:admin:js', function () {
    return gulp.watch([
        'src/AdminModule/assets/js/**',
        'src/assets/js/**'
    ], [
        'admin:js'
    ]);
});

gulp.task('public', ['public:js', 'public:css', 'public:fonts']);
gulp.task('admin', ['admin:js', 'admin:css', 'admin:fonts']);
gulp.task('css', ['public:css', 'admin:css']);
gulp.task('js', ['public:js', 'admin:js']);
gulp.task('watch:public', ['watch:public:css', 'watch:public:js']);
gulp.task('watch:admin', ['watch:admin:css', 'watch:admin:js']);
gulp.task('watch:css', ['watch:public:css', 'watch:admin:css']);
gulp.task('watch:js', ['watch:public:js', 'watch:admin:js']);
gulp.task('watch', ['watch:public', 'watch:admin']);
gulp.task('default', ['public', 'admin']);
