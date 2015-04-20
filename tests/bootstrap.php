<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

define('DS', DIRECTORY_SEPARATOR);

// Paths
define('PATH_ROOT', realpath(dirname(__FILE__). DS . '..' . DS));
define('PATH_VENDOR', PATH_ROOT . DS . 'vendor');
define('PATH_TMP', PATH_ROOT . DS . 'tests' . DS . 'Resources' . DS . 'tmp');
define('BLUZ_ENV', 'dev');

// register composer autoloader
require_once PATH_VENDOR . '/autoload.php';