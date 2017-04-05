Bluzman - Simple workflow manager for Bluz Framework
======================================
Bluzman is a set of command-line tools which provides a simple workflow with an application based and mantained by Bluz framework.

## Achievements

[![Build Status](https://secure.travis-ci.org/bluzphp/bluzman.png?branch=master)](https://travis-ci.org/bluzphp/bluzman)
[![Dependency Status](https://www.versioneye.com/user/projects/58cbb18f6893fd004792c5da/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58cbb18f6893fd004792c5da)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bluzphp/bluzman/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bluzphp/bluzman/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/bluzphp/bluzman/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bluzphp/bluzman/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/bluzphp/bluzman/v/stable.png)](https://packagist.org/packages/bluzphp/bluzman)
[![Total Downloads](https://poser.pugx.org/bluzphp/bluzman/downloads.png)](https://packagist.org/packages/bluzphp/bluzman)

[![License](https://poser.pugx.org/bluzphp/bluzman/license.svg)](https://packagist.org/packages/bluzphp/bluzman)

Features
-------------------------
* Code-generator of application components
* Shorthand for phinx and composer tools (TODO)
* Shorthand for built-in web-server

Requirements
-------------------------
* OS: Linux
* PHP: 7.0 (or later)

Usage
-------------------------
List of available commands
```bash
php ./vendor/bin/bluzman list
```

## Code generators

### Model generator

For create new model you must run the command in terminal:
```bash
bluzman generate:model model_name table_name
```

 - _model_name_ - the name of model. With this name will be created folder of model.
 - _table_name_ - the name of databases table for pattern formation properties object model.


### Module generator

For create new module you must run the command in terminal
```bash
bluzman generate:module module_name [controller_name]...
```

 - _module_name_ - the name of module. With this name will be created folder of module.
 - _controller_name_ - the name(s) of controller(s). With this name will be created controller and view. Optional.

### Controller generator

For create new controller you must run the command in terminal:
```bash
bluzman generate:controller module_name controller_name
```

 - _module_name_ - the name of module. With this name will be created folder of module.
 - _controller_name_ - the name of controller. With this name will be created controller and view.
 
### CRUD generator

For create CRUD class you must run the command in terminal:

```bash
bluzman generate:crud model_name 
```
Generator will create a class in `model_name/Crud.php`

If you want to generate CRUD controller and view you must run the command in terminal:

```bash
bluzman generate:crud model_name module_name
```

Generator will create a controller in `module_name/controllers/crud.php` and a view `module_name/views/crud.php`

### REST generator

For create REST controller you must run the command in terminal:

```bash
bluzman generate:rest model_name module_name
```

Generator will create a controller in `module_name/controllers/rest.php`.
 
### GRID generator

For create GRID class you must run the command in terminal:

```bash
bluzman generate:grid model_name 
```
Generator will create a class in `model_name/Grid.php`

If you want to generate GRID controller and view you must run the command in terminal:

```bash
bluzman generate:grid model_name module_name
```

Generator will create a controller in `module_name/controllers/grid.php` and a view `module_name/views/grid.php`

## Migrations
> All `db:*` commands is just shorthand to call `php /vendor/bin/phinx command -e default -c phinx.php`. 

### Status
```bash
bluzman db:status
```

## Install and remove modules

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

## Server

Bluzman provides a commands list to operate with built-in PHP server.

To launch built-in PHP server you must run the command in terminal:
```bash
bluzman server:start [--host[="..."]] [--port=["..."]]
```
By default server will be available by the address **0.0.0.0:8000** and you will see all logs in the terminal.

But there is an option to run server in the background, this requires an option **-b**:

```bash
bluzman server:start ... -b
```

And if server launched in the background, it can be stopped with following command:
```bash
bluzman server:stop [--host[="..."]] [--port=["..."]]
```

If you want to know the status of the server you must run the command in terminal:
```bash
bluzman server:status [--host[="..."]] [--port=["..."]]
```
