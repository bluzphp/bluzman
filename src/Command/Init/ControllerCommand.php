<?php

namespace Bluzman\Command\Init;

use Bluzman\Command;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Respect\Validation\Validator as v;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ControllerCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 1:58 PM
 */

class ControllerCommand extends Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'init:controller';

    /**
     * @var string
     */
    protected $description = 'Initialize a new controller';

    protected function getOptions()
    {
        return [
            ['module', null, InputOption::VALUE_OPTIONAL, 'The name of module.', null, v::alnum('-')->noWhitespace()],
            ['name', null, InputOption::VALUE_OPTIONAL, 'The name of new controller.', null, v::alnum('-')->noWhitespace()]
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->info("Running \"init:controller\" command"));

        $this->generate()->verify();

        $output->writeln("Controller \"" . $this->info($this->getOption('name')) . "\"" .
                " has been successfully created in the module \"" . $this->info($this->getOption('module')) . "\".");
    }

    /**
     * @param $controllerName
     * @param $moduleName
     */
    protected function generate()
    {
        $template = new Generator\Template\ControllerTemplate;
        $template->setFilePath($this->getFilePath());

        $generator = new Generator\Generator($template);
        $generator->make();

        return $this;
    }

    /**
     * @return string
     */
    protected function getFilePath()
    {
        return $this->getApplication()->getWorkingPath()
            . DS . 'application'
            . DS . 'modules'
            . DS . $this->getInput()->getOption('module')
            . DS . 'controllers'
            . DS . $this->getInput()->getOption('name')
            . '.php';
    }

    /**
     * Verify command result
     */
    public function verify()
    {
        $fs = new Filesystem();

        if (!$fs->exists($this->getFilePath())) {
            throw new \RuntimeException("Something is wrong. Controller was not created");
        }

        return true;
    }
}