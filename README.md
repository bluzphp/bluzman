Bluzman - Simple workflow manager for Bluz Framework
======================================
Bluzman is a set of command-line tools which provides a simple workflow with an application based and maintained by Bluz framework.

## Achievements

[![PHP >= 7.1+](https://img.shields.io/packagist/php-v/bluzphp/bluzman.svg?style=flat)](https://php.net/)

[![Latest Stable Version](https://img.shields.io/packagist/v/bluzphp/bluzman.svg?label=version&style=flat)](https://packagist.org/packages/bluzphp/bluzman)

[![Build Status](https://img.shields.io/travis/bluzphp/bluzman/master.svg?style=flat)](https://travis-ci.org/bluzphp/bluzman)

[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/bluzphp/bluzman.svg?style=flat)](https://scrutinizer-ci.com/g/bluzphp/bluzman/)

[![Coverage Status](https://img.shields.io/coveralls/bluzphp/bluzman/master.svg?style=flat)](https://coveralls.io/r/bluzphp/bluzman?branch=master)

[![Total Downloads](https://img.shields.io/packagist/dt/bluzphp/bluzman.svg?style=flat)](https://packagist.org/packages/bluzphp/bluzman)

[![License](https://img.shields.io/packagist/l/bluzphp/bluzman.svg?style=flat)](https://packagist.org/packages/bluzphp/bluzman)

## Features
* Code-generator of the application components
* Shorthand for phinx and composer tools
* Shorthand for built-in web-server

## Requirements
* OS: Linux
* PHP: 7.1 (or later)

## Usage
List of available commands
```bash
php ./vendor/bin/bluzman list
```

## Code generators

All generators don't rewrite exists files, but you can run generate command with flag `--force`, to rewrite all of them

### Model generator

For create new model you should run the following command in the terminal:
```bash
bluzman generate:model model_name table_name
```

 - _model_name_ - the name of model. With this name will be created folder of model.
 - _table_name_ - the name of databases table for pattern formation properties object model.


### Module generator

For create new module you should run the following command in the terminal:
```bash
bluzman generate:module module_name [controller_name]...
```

 - _module_name_ - the name of module. With this name will be created folder of module.
 - _controller_name_ - the name(s) of controller(s). With this name will be created controller and view. Optional.

### Controller generator

For create new controller you should run the following command in the terminal:
```bash
bluzman generate:controller module_name controller_name
```

 - _module_name_ - the name of module. With this name will be created folder of module.
 - _controller_name_ - the name of controller. With this name will be created controller and view.
 
### CRUD generator

For create CRUD class you should run the following command in the terminal:

```bash
bluzman generate:crud model_name 
```
Generator will create a class in `model_name/Crud.php`

If you want to generate CRUD controller and view you should run the next command:

```bash
bluzman generate:crud model_name module_name
```

Generator will create a controller in `module_name/controllers/crud.php` and a view `module_name/views/crud.php`

### REST generator

For create REST controller you should run the following command in the terminal:

```bash
bluzman generate:rest model_name module_name
```

Generator will create a controller in `module_name/controllers/rest.php`.
 
### GRID generator

For create GRID class you should run the following command in the terminal:

```bash
bluzman generate:grid model_name 
```
Generator will create a class in `model_name/Grid.php`

If you want to generate GRID controller and view you should run the following command in the terminal:

```bash
bluzman generate:grid model_name module_name
```

Generator will create a controller in `module_name/controllers/grid.php` and a view `module_name/views/grid.php`

### All-in-one generator - scaffold

Generator `scaffold` will generate:

* [Model](#model-generator)
* [Module](#module-generator)
* [Crud](#crud-generator)
* [Grid](#grid-generator)

For generate all of them run the following command in the terminal:

```bash
bluzman generate:scaffold model_name table_name module_name
```

## Migrations
> All `db:command` commands is just shorthand to call `php /vendor/bin/phinx command -e default -c phinx.php`

### Status
```bash
bluzman db:status
```

### Create migration
```bash
bluzman db:create UsersTable
```

### Migrate
```bash
bluzman db:migrate
```

### Rollback last migration
```bash
bluzman db:rollback
```

### Create seed
```bash
bluzman db:seed:create UserSeed
```

### Apply seed data
```bash
# all seed
bluzman db:seed:run
# specified seed
bluzman db:seed:run UserSeed
```

## Install and remove modules

> Information about available modules will retrieve from https://github.com/bluzphp by `bluz-module` tag

Retrieve available modules:
```bash
bluzman module:list
```

Install module:
```bash
bluzman module:install auth
```

Remove module:
```bash
bluzman module:remove auth
```

## Tests
For run all tests:
```bash
bluzman test
```

For run specified group:
```bash
bluzman test module-options
```

## Server

Bluzman provides a commands list to operate with built-in PHP server.

To launch built-in PHP server you should run the following command in the terminal:
```bash
bluzman server:start --host[="..."] --port[="..."]
```
By default server will be available by the address **0.0.0.0:8000** and you will see all logs in the terminal.

But there is an option to run server in the background, this requires an option **-b**:

```bash
bluzman server:start ... -b
```

And if server launched in the background, it can be stopped with following command:
```bash
bluzman server:stop --host[="..."] --port[="..."]
```

If you want to know the status of the server you should run the command in the terminal:
```bash
bluzman server:status --host[="..."] --port[="..."]
```
