<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Application;

use Bluzman\Command;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Composer;

/**
 * @package Bluzman\Application
 *
 * @author Pavel Machekhin
 * @created 2013-11-28 12:31
 */
class Application extends Console\Application
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Connection to
     *
     * @var \PDO
     */
    protected $conn = null;

    /**
     * @param \Bluzman\Application\Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return \Bluzman\Application\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->setConfig(new Config($this));
        $this->registerCommands();
    }

    /**
     * Returns the path to the directory with bluzman application
     *
     * @return string
     */
    public function getWorkingPath()
    {
        return getcwd();
    }

    /**
     * Returns the path to the .bluzman directory
     *
     * @return string
     */
    public function getBluzmanPath()
    {
        return $this->getWorkingPath() . DS . '.bluzman';
    }

    /**
     * Removed some commands from default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(
            [
                new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
                new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The environment to be used.', 'dev'),
                new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
                new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Do not output any message.'),
                new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of messages.'),
                new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version.')
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        // register helpers

        return $helperSet;
    }

    /**
     * Register Bluzman commands
     *
     * @todo Find a way to do this automatically
     */
    protected function registerCommands()
    {
        $this->addCommands(
            [
                new Command\Server\StartCommand,
                new Command\Server\StopCommand,
                new Command\Server\StatusCommand,
                new Command\TestCommand,
                new Command\Init\AllCommand,
                new Command\Init\ModuleCommand,
                new Command\Init\ControllerCommand,
                new Command\Init\ModelCommand
            ]
        );
    }

    /**
     * Returns a \PDO connection of actual Bluz Skeleton application.
     *
     * @return \PDO
     * @throws \Bluz\Config\ConfigException
     * @todo Should we move it to Generator Model?
     */
    public function getDbConnection()
    {
        if ($this->conn === null) {
            $conf = $this->getConfig()->getBluzConfig(BLUZ_ENV)->getData('db')['connect'];
            $dsn = "$conf[type]:host=$conf[host];dbname=$conf[name]";
            $this->conn = new \PDO($dsn, $conf['user'], $conf['pass']);
        }
        return $this->conn;
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isModuleExists($moduleName)
    {
        $pathDir = $this->getWorkingPath() . DIRECTORY_SEPARATOR
            . 'application' . DIRECTORY_SEPARATOR
            . 'modules' . DIRECTORY_SEPARATOR
            . $moduleName;

        return is_dir($pathDir);
    }

    /**
     * @param $modelName
     * @return bool
     */
    public function isModelExists($modelName)
    {
        $pathDir = $this->getWorkingPath() . DIRECTORY_SEPARATOR
            . 'application' . DIRECTORY_SEPARATOR
            . 'models' . DIRECTORY_SEPARATOR
            . $modelName;
        return is_dir($pathDir);
    }
}
