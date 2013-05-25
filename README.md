  Bluzman - Simple workflow with Bluz framework
======================================

Bluzman is a set of command-line tools which provides a simple workflow with an application based and mantained by Bluz framework.

## Features

 * Application scaffolding
 * Code-generator of application components

## Requirements

 * OS: Linux
 * PHP: 5.4 (or later)

## Installation

It is recommended to add bluzman to your PATH variable, which specifies where executable files are located.
This will provide an ability to use one bluzman installation with many applications.

**Steps to install**

1. Clone from repository

    ```bash
    $ git clone git://github.com/bashmach/bluzman.git
    $ cd bluzman
    ```

2.  Install dependencies with composer

    ```bash
    $ composer install
    ```

3.  Finish install - add bluzman to PATH variable.

    You can skip this, however you will need to use a fullpath to bluzman script from ``` bin ``` directory.

    ```bash
    $ sh ./bin/install.sh
    ```

    Start new session in your terminal or run this command in current session:
    ```bash
    $ export PATH=$PATH:/%path_to_bluzman_directory%/bin
    ```

## Usage

List of available commands

```bash
$ bluzman list
```

### Scaffold application

Create new project from bluzphp/skeleton by composer.

```bash
$ bluzman init:all
```

### Model generator

```bash
$ bluzman init:model
```

### Module generator

```bash
$ bluzman init:module
```

### Controller generator

```bash
$ bluzman init:controller
```

### Start server

```bash
$ bluzman server
```

## TODO

 * Install Bluz modules (which are not created yet =))
 * Tests launcher
 * Deploying (tool to use?)





