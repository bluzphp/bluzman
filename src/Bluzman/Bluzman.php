<?php

/**
 * @namespace
 */
namespace Bluzman;

use Bluzman\Command\Init;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Bluzman
 *
 * @category Bluzman
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/20/13 12:39 PM
 */
class Bluzman extends Application
{
    const ENVIRONMENT_DEVELOPMENT = 'dev';
    const ENVIRONMENT_PRODUCTION = 'prod';

    protected $config;

    protected $directoryName = '.bluzman';

    protected $configName = 'config.json';

    protected $modulesPath = 'application/modules';

    /**
     * @var \PDO
     */
    protected $dbh;

    /**
     * @todo Read all commands from `src` folder
     *
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->add(new Init\AllCommand);
        $this->add(new Init\ControllerCommand);
        $this->add(new Init\ModelCommand);
        $this->add(new Init\ModuleCommand);
        $this->add(new \Bluzman\Command\TestCommand);
    }

    /**
     * Removed some commands from default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

            new InputOption('--help',           '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--quiet',          '-q', InputOption::VALUE_NONE, 'Do not output any message.'),
            new InputOption('--verbose',        '-v', InputOption::VALUE_NONE, 'Increase verbosity of messages.'),
            new InputOption('--version',        '-V', InputOption::VALUE_NONE, 'Display this application version.')
        ));
    }

    /**
     * Returns config data or throw an exception when it is not found
     *
     * @throws \RuntimeException
     * @return mixed
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->readConfig();
        }

        return $this->config;
    }

    /**
     * Fetch the config data
     *
     * @throws \RuntimeException
     */
    protected function readConfig()
    {
        $configPath = $this->getConfigPath();

        if (!is_readable($configPath)) {
            throw new \RuntimeException('Unable to find bluzman config in current directory.');
        }

        $this->config = json_decode(file_get_contents($this->getConfigPath()));

        file_put_contents($this->getConfigPath(), json_encode($this->config, JSON_PRETTY_PRINT));
    }

    /**
     * Generate config in new application directory
     *
     * @param $projectName
     */
    public function generateConfig($projectName, $environment = 'dev')
    {
        $bluzmanDirectory = $this->getPath() . DIRECTORY_SEPARATOR . $this->directoryName;

        if (!is_dir($bluzmanDirectory)) {
            mkdir($bluzmanDirectory, 0755);
        }

        if (!is_readable($this->getConfigPath())) {
            file_put_contents($this->getConfigPath(),
<<<EOF
{
  "name": "$projectName",
  "environment": "$environment",
  "version": "0.0.1"
}
EOF
            );
        }

        $this->installComposerLocally();
    }

    /**
     * Returns the path to the application config
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->directoryName . DIRECTORY_SEPARATOR . $this->configName;
    }

    /**
     * Returns the path to application directory
     *
     * @return string
     */
    public function getPath()
    {
        return getcwd();
    }

    /**
     * @param $name
     * @return string
     */
    public function getModulePath($name)
    {
        $path = $this->getPath() . DIRECTORY_SEPARATOR . $this->modulesPath;

        return $path . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * @param $name
     * @return bool
     */
    public function isModuleExists($name)
    {
        if (is_dir($this->getModulePath($name))) {
            return true;
        }

        return false;
    }

    /**
     * Returns the path to the composer.
     * Download composer.phar when it's missed at the system.
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getComposerCmd()
    {
        $cmd = 'composer';

        $verifyFile = './.verifyComposer';

        // check if composer installed at system
        // @todo Is there any better way to implement this?
        $composerExist = ((int) trim(shell_exec("touch $verifyFile && $cmd&>$verifyFile | cat $verifyFile | grep 'not found' | wc -w")));

        shell_exec("rm $verifyFile");

        // download composer if it's not found
        if (!empty($composerExist)) {
            // if composer was already installed in .bluzman directory
            $composerPath = dirname($this->getConfigPath()) . DIRECTORY_SEPARATOR . 'composer.phar';

            if (is_file($composerPath)) {
                return $composerPath;
            }

            $composerPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'composer.phar';

            // download to temporary folder
            exec('cd ' . sys_get_temp_dir() .' && curl -sS https://getcomposer.org/installer | php', $out);

            if (!is_readable($composerPath)) {
                throw new \RuntimeException('Unable to download composer.phar');
            }

            chmod($composerPath, 0755);

            $cmd = $composerPath;

            $this->installComposerLocally();
        }

        return $cmd;
    }

    /**
     * Install composer.phar to .bluzman directory if project was initialized
     */
    protected function installComposerLocally()
    {
        // if composer is not installed globally - move it to .bluzman directory
        $composerPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'composer.phar';

        if (is_file($composerPath) && is_file($this->getConfigPath())) {
            rename($composerPath, dirname($this->getConfigPath()) . DIRECTORY_SEPARATOR . 'composer.phar');
        }
    }

    /**
     * Get config of application generated by bluzman.
     *
     * @return \Bluz\Config\Config
     */
    public function getApplicationConfig()
    {
        if (!isset($this->getConfig()->environment)) {
            throw new \LogicException('Missed "environment" value in bluzman configuration file');
        }

        //Init Bluz config

        define('DEBUG', true);
        define('PATH_APPLICATION', $this->getPath() . DIRECTORY_SEPARATOR . 'application');
        define('PATH_DATA', $this->getPath() . DIRECTORY_SEPARATOR . 'data');

        $loader = $this->getPath() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

        include $loader;

        $config = new \Bluz\Config\Config();
        $config->load($this->getConfig()->environment);

        return $config;
    }

    /**
     * Initialize PDO with application config
     *
     * @return \PDO
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function getDbConnection()
    {
        if (!$this->dbh) {
            $config = $this->getApplicationConfig()->get('db');

            if (!isset($config['connect'])) {
                throw new \LogicException(
                    'Invalid database config for '
                        . '"' . $this->getConfig()->environment . '" '
                        . 'environment.'
                );
            }

            $dbConfig = $config['connect'];
            unset($config);

            try {
                $this->dbh = new \PDO(
                    $dbConfig['type'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['name'],
                    $dbConfig['user'],
                    $dbConfig['pass']
                );
            } catch (\PDOException $e) {
                throw new \RuntimeException('Wrong database credentials in bluzman config.');
            } catch (\Exception $e) {
                throw new \RuntimeException('Unknown error.');
            }
        }

        return $this->dbh;
    }

    /**
     * @throws \LogicException
     */
    public function callForContribute($output)
    {
        $output->writeln('');
        $output->writeln("<error> This command is not implemented yet. Don't be indifferent - you can contribute! https://github.com/bluzphp. </error>");
        $output->writeln('');
    }
}
