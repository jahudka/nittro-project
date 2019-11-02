"use strict";

const gulp = require('gulp');
const publicTasks = require('./public');
const adminTasks = require('./admin');

const categories = [
    ['public', publicTasks],
    ['admin', adminTasks],
];

for (const [category, tasks] of categories) {
    for (const [name, task] of Object.entries(tasks)) {
        exports[name === 'default' ? category : `${category}:${name}`] = task;
    }
}

exports.default = gulp.parallel(publicTasks.default, adminTasks.default);
exports.watch = gulp.parallel(publicTasks.watch, adminTasks.watch);
