<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Init;

use Bluzman\Application\Application;
use Bluzman\Command;
use Respect;
use Respect\Validation\Validator as v;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ModuleCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-04-05 21:57
 */

class ModuleCommand extends Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'init:module';

    /**
     * @var string
     */
    protected $description = 'Initialize a new module';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @param $fileSystem
     */
    public function setFs($fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public function getFs()
    {
        return $this->fileSystem;
    }

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setFs(new Filesystem);
    }

    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_OPTIONAL, 'The name of module.', null, v::alnum('_-')->noWhitespace()]
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->info('Running "init:module" command'));

        // create main folder and subfolders
        $this->initModuleStructure($this->getOption('name'));

        $this->verify($input, $output);

        $output->writeln('Module ' . $this->info($this->getOption('name')) . ' has been successfully created.');
    }

    /**
     *
     *
     * @param $name
     */
    protected function initModuleStructure($name)
    {
        $this->addSubFolders(
            $this->getModulePath($name),
            [
                'controllers',
                'views'
            ]
        );
    }

    /**
     *
     *
     * @param $path
     * @param array $subfolders
     */
    protected function addSubFolders($path, array $subfolders = array())
    {
        if (!$this->getFs()->exists($path)) {
            $this->getFs()->mkdir($path);
        }

        foreach ($subfolders as $subfolderName) {
            $subfolderPath = $path . DIRECTORY_SEPARATOR . $subfolderName;

            $this->getFs()->mkdir($subfolderPath, 0755);
            $this->getFs()->touch([$subfolderPath . DIRECTORY_SEPARATOR . '.keep']);
        }
    }

    /**
     * @param $name
     * @return string
     */
    protected function getModulePath($name)
    {
        return $this->getApplication()->getWorkingPath()
            . DS . 'application'
            . DS . 'modules'
            . DS . $name;
    }

    /**
     * @todo Revert if not verified
     *
     * @return bool
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $modulesPath = $this->getApplication()->getWorkingPath() . DS . 'application' . DS . 'modules';

        $paths = [
            $modulesPath,
            $modulesPath . DS . $input->getOption('name'),
            $modulesPath . DS . $input->getOption('name') .  DS . 'controllers',
            $modulesPath . DS . $input->getOption('name') .  DS . 'views'
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                return false;
            }
        }

        return true;
    }
}
