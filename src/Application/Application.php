<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Application;

use Bluz\Config\Config;
use Bluz\Proxy;
use Bluzman\Command;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @package Bluzman\Application
 *
 * @author Pavel Machekhin
 * @created 2013-11-28 12:31
 */
class Application extends Console\Application
{
    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->registerCommands();
    }

    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        // Init Bluz config
        $config = new Config();
        $config->setPath(PATH_APPLICATION);
        $config->setEnvironment(BLUZ_ENV);
        $config->init();

        Proxy\Config::setInstance($config);
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
     * Register Bluzman commands
     *
     * @todo Find a way to do this automatically
     */
    protected function registerCommands()
    {
        $this->addCommands(
            [
                new Command\MagicCommand,
                new Command\Generate\ModuleCommand,
                new Command\Generate\ControllerCommand,
                new Command\Generate\ModelCommand,
                new Command\Generate\CrudCommand,
//                new Command\Server\StartCommand,
//                new Command\Server\StopCommand,
//                new Command\Server\StatusCommand,
            ]
        );
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
     * Get Module path
     *
     * @param  string $name
     * @return string
     */
    public function getModulePath($name)
    {
        return $this->getWorkingPath() . DIRECTORY_SEPARATOR
            . 'application' . DIRECTORY_SEPARATOR
            . 'modules' . DIRECTORY_SEPARATOR
            . $name;
    }

    /**
     * Get Model path
     *
     * @param  string $name
     * @return string
     */
    public function getModelPath($name)
    {
        return $this->getWorkingPath() . DIRECTORY_SEPARATOR
            . 'application' . DIRECTORY_SEPARATOR
            . 'models' . DIRECTORY_SEPARATOR
            . $name;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function isModuleExists($name)
    {
        return is_dir($this->getModulePath($name));
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function isModelExists($name)
    {
        return is_dir($this->getModelPath($name));
    }
}
