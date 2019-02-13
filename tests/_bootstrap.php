<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Root path, double level up
// UP to root from "/tests/"
$root = realpath(dirname(__DIR__));

// Definitions
define('PATH_ROOT', $root);
define('PATH_VENDOR', PATH_ROOT . DS . 'vendor');
define('PATH_TMP', PATH_ROOT . DS . 'tests' . DS . 'Resources' . DS . 'tmp');
define(
    'PATH_APPLICATION',
    PATH_ROOT . DS . 'tests' . DS . 'Application' . DS . 'Resources' . DS . 'fixtures' . DS . 'app' . DS . 'application'
);
define('BLUZ_ENV', 'dev');

// register composer autoloader
$loader = require PATH_VENDOR . '/autoload.php';
$loader->addPsr4('Bluzman\\Tests\\', __DIR__);
