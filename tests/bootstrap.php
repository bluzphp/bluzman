<?php

define('DS', DIRECTORY_SEPARATOR);

// Paths
define('PATH_ROOT', realpath(dirname(__FILE__). DS . '..' . DS));
define('PATH_VENDOR', PATH_ROOT . DS . 'vendor');
define('PATH_TMP', PATH_ROOT . DS . 'tests' . DS . 'Bluzman' . DS . 'Tests' . DS . 'tmp');

// register composer autoloader
require_once PATH_VENDOR . '/autoload.php';