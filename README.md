Nittro project skeleton
=======================

This is a simple skeleton for a Nette project with Nittro via Gulp,
Doctrine via Kdyby and a basic admin template based on Bootstrap v3.
PHP 7.1 is required.

### Installation

```bash
composer create-project jahudka/nittro-project myproject
cd myproject
npm install
gulp
cp etc/config.local.dist etc/config.local.neon
$EDITOR etc/config.local.neon
bin/console orm:schema-tool:create
```

### Application layout
 - Configuration is stored in `./etc`.
 - Executable console tool is at `./bin/console`.
 - All application sources are under `./src`, which is a PSR-4 namespace
   root for the `App` namespace; autoloading in this namespace is provided
   by Composer.
 - All caches and logs are under `./var`.

### Doctrine
 - Entities are in `./src/Entity`.
 - A default entity exists for user identity, users can be managed
   using the console tool's `user:*` family of commands.

### Components
 - There is an abstract base class `App\UI\BaseControl` which adds
   the `render()` method; components extending this class will be
   rendered using a latte template of the same name (lc-first) as
   the component's class name without namespace, located either at
   `./templates` or `./` relative from the file the component is
   defined in, e.g. the `App\AdminModule\Forms\LoginForm` component has
   a template at `src/AdminModule/Forms/templates/loginForm.latte`.
 - As a convenience, all interfaces with a single method called `create`
   defined in either the common `./src/Factories` or the per-module
   `Factories` directories are automatically registered in the DI container
   (unless a service implementing the interface already exists).

### Assets
 - All assets are managed and compiled using Gulp.
 - As a convention, assets specific to each module are in the
   module's `assets` subdirectory while (potentially) common
   assets are in the `./src/assets` directory.
 - There are many available Gulp tasks to build all the individual
   assets, as well as a couple of watch tasks to make life easier
   during development. They are named according to the following
   convention:
   ```
   task := [ watch ] [:] [ public|admin ] [:] [ js|css|fonts ]
   ```
   You can think of the task names as a list of tags separated by
   a colon. The `watch` tag doesn't work with the `fonts` tag,
   but otherwise all combinations are allowed. Omitting any tag except
   the `watch` tag is the same as if you specified all the permutations of 
   the tag, e.g. `watch:js` is the same as `watch:public:js watch:admin:js`
   and `public` is the same as `public:js public:css public:fonts`.
 - The default task builds everything. 
 - Nittro components are enabled at the beginning of the gulpfile
   using configuration flags of the builder instances; additional
   libraries from other vendors as well as your custom code can be
   specified there as well. Reading the source of the gulpfile should
   make everything clear.
 - All non-minified javascripts are minified, all LESS styles are
   compiled and all non-minified styles are minified.
 - Bootstrap for Nittro is generated automatically.

### Console
 - There are a number of useful Doctrine-related commands defined
   by the `kdyby/doctrine` package - those are available as usual.
 - There are also a couple of commands for user management as mentioned
   previously.
 - As a convenience, all classes in the common `./src/Commands` or
   the per-module `Commands` directories that extend Symfony's `Command`
   class are automatically registered in the DI container and tagged
   with the `kdyby.console.command` tag (unless a service of the
   same type already exists).
