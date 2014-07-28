<?php

namespace Bluzman\Command\Init;

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
 * @created  4/05/13 09:57 PM
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
    protected $fs;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem $fs
     */
    public function setFs($fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public function getFs()
    {
        return $this->fs;
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
        $output->writeln('<info>Running "init:module" command</info>');

        $name = $this->getOption('name');

        // create main folder and subfolders
        $this->createModule($name);

        $this->verify($input, $output);

        $output->writeln('Module <info>"' . $name . '"</info> has been successfully created.');
    }

    /**
     *
     *
     * @param $name
     */
    protected function createModule($name)
    {
        $path = $this->getModulePath($name);

        try {
            $this->getFs()->mkdir($path);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }

        $this->addSubFolders($path, array('controllers', 'views'));
    }

    /**
     *
     *
     * @param $path
     * @param array $subfolders
     */
    protected function addSubFolders($path, array $subfolders = array())
    {
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
     * @todo
     *
     * @return bool
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');

        if (!$this->getFs()->exists($this->getModulePath($name))) {
            throw new \RuntimeException('Failed to create new module ' . $name . '.');
        }

        return true;
    }
}