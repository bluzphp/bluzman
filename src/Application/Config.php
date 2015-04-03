<?php
/**
 * @created 2013-11-28 12:26
 * @author Pavel Machekhin <pavel.machekhin@gmail.com>
 */

namespace Bluzman\Application;

/**
 * Class Config
 * @package Bluzman\Application
 */
class Config
{
    /**
     * @var \Composer\Json\JsonFile
     */
    protected $configFile;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Application
     */
    protected $application;

    /**
     * @param \Composer\Json\JsonFile $configFile
     */
    public function setConfigFile($configFile)
    {
        $this->configFile = $configFile;
    }

    /**
     * @return \Composer\Json\JsonFile
     */
    public function getConfigFile()
    {
        if (!$this->configFile) {
            $configPath = $this->getConfigPath();

            $this->setConfigFile(new \Composer\Json\JsonFile($configPath));
        }

        return $this->configFile;
    }

    /**
     * @param \Bluzman\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return \Bluzman\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->readOptions());
        }
        return $this->options;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name)
    {
        $options = $this->getOptions();

        return isset($options[$name]) ? $options[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $options = $this->getOptions();

        $options[$name] = $value;

        $this->putOptions($options);
    }

    public function unsetOption($name)
    {
        $options = $this->getOptions();

        unset($options[$name]);

        $this->putOptions($options);
    }

    /**
     * @param array $options
     * @throws \RuntimeException
     */
    public function putOptions(array $options)
    {
        $configPath = $this->getConfigPath();

        if (!is_writable(dirname($configPath))) {
            throw new \RuntimeException($configPath . 'Unable to create bluzman config in current directory.');
        }

        $this->getConfigFile()->write($options);

        $this->options = null;
        $this->getOptions();
    }

    /**
     * Read config file
     *
     * @throws \RuntimeException
     */
    protected function readOptions()
    {
        return $this->getConfigFile()->read();
    }

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }

    /**
     * @param $name
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        return $this->setOption($name, $value);
    }

    public function getConfigPath()
    {
        return $this->getApplication()->getBluzmanPath() . DS . 'config.json';
    }

    /**
     * Get config of the application
     *
     * @return \Bluz\Config\Config
     */
    public function getBluzConfig($env)
    {
        $path = $this->getApplication()->getWorkingPath();

        //Init Bluz config
        defined('DEBUG') ? : define('DEBUG', true);
        defined('PATH_APPLICATION') ? : define('PATH_APPLICATION', $path . DS . 'application');
        defined('PATH_PUBLIC') ? : define('PATH_PUBLIC', $path . DS . 'public');
        defined('PATH_DATA') ? : define('PATH_DATA', $path . DS . 'data');

        $config = new \Bluz\Config\Config();
        $config->setPath(PATH_APPLICATION);
        $config->setEnvironment($env);
        $config->init();

        return $config;
    }
}