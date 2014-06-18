<?php
/**
 * @created 06/18/14 4:19PM
 * @author Pavel Machekhin <pavel.machekhin@gmail.com>
 */

namespace Bluzman\Migrations\Provider;

use Symfony\Component\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Bluzman\Application\Application;
use Bluzman\Helper\ArrayHelper;

class Phinx
{
    /**
     * @var string
     */
    protected $env;

    /**
     * @var \Bluzman\Application\Application
     */
    protected $application;

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
     * @param string $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param Application $application
     * @param string $env
     */
    public function __construct(Application $application, $env = 'dev')
    {
        $this->setApplication($application);
        $this->setEnv($env);

        $this->updateConfig();
    }

    /**
     *
     */
    protected function getConfig()
    {
        $bluzConfig = $this->getApplication()->getConfig()->getBluzConfig($this->getEnv())->getData('db', 'connect');

        return [
            'paths' =>[
                'migrations' => $this->getApplication()->getWorkingPath() . DS . 'migrations'
            ],
            'environments' => [
                'default_migration_table' => 'phinxlog',
                'default_database' => $this->getEnv(),
                $this->getEnv() => [
                    'adapter' => ArrayHelper::get($bluzConfig, 'type', 'mysql'),
                    'host' => ArrayHelper::get($bluzConfig, 'host'),
                    'name' => ArrayHelper::get($bluzConfig, 'name'),
                    'user' => ArrayHelper::get($bluzConfig, 'user'),
                    'pass' => ArrayHelper::get($bluzConfig, 'pass'),
                    'port' => ArrayHelper::get($bluzConfig, 'port', 3306),
                    'charset' => ArrayHelper::get($bluzConfig, 'charset', 'utf8')
                ]
            ]
        ];
    }

    public function getConfigPath()
    {
        return $this->getApplication()->getBluzmanPath() . DS . 'phinx.' . $this->getEnv() . '.yml';
    }

    /**
     *
     */
    protected function updateConfig()
    {
        $dumper = new Yaml\Dumper();

        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $this->getConfigPath(),
            $dumper->dump($this->getConfig(), 2)
        );
    }
}