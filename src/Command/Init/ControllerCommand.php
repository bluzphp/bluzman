<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Init;

use Bluzman\Command;
use Bluzman\Generator;
use Bluzman\Input\InputException;
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
 * @created  2013-03-28 13:58
 */
class ControllerCommand extends Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'init:controller';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $description = 'Initialize a new controller';

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setFs(new Filesystem);
    }

    /**
     * @param $fs
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

    protected function getOptions()
    {
        return [
            ['module', null, InputOption::VALUE_OPTIONAL, ' name of module.', null, v::alnum('-')->noWhitespace()],
            ['name', null, InputOption::VALUE_OPTIONAL, ' name of new controller.', null, v::alnum('-')->noWhitespace()]
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InputException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln($this->info("Running \"init:controller\" command"));

            $moduleName = strtolower($this->getOption('module'));

            if (!$this->getApplication()->isModuleExists($moduleName)) {
                $output->writeln("<error>ERROR: Module " . $moduleName . " is not exist</error>\n");
            } else {

                $this->generate()->verify();

                $output->writeln(
                    "Controller \"" . $this->info($this->getOption('name')) . "\"" .
                    " has been successfully created in the module \"" . $this->info($this->getOption('module')) . "\"."
                );
            }
        } catch (InputException $e) {
            $output->writeln("<error>ERROR: {$e->getMessage()}</error>\n");
            $this->execute($input, $output);
        }
    }

    /**
     * @return $this
     */
    protected function generate()
    {
        $template = new Generator\Template\ControllerTemplate;
        $template->setFilePath($this->getFilePath());

        $generator = new Generator\Generator($template);
        $generator->make();

        $template = new Generator\Template\ViewTemplate;
        $template->setFilePath($this->getViewPath());
        $template->setTemplateData(['name' => $this->getOption('name')]);

        $generator = new Generator\Generator($template);
        $generator->make();

        return $this;
    }

    /**
     * @return string
     * @throws InputException
     */
    protected function getFilePath()
    {
        return $this->getApplication()->getWorkingPath()
            . DS . 'application'
            . DS . 'modules'
            . DS . $this->getOption('module')
            . DS . 'controllers'
            . DS . $this->getOption('name')
            . '.php';
    }

    /**
     * @return string
     * @throws InputException
     */
    protected function getViewPath()
    {
        return $this->getApplication()->getWorkingPath()
            . DS . 'application'
            . DS . 'modules'
            . DS . $this->getOption('module')
            . DS . 'views'
            . DS . $this->getOption('name')
            . '.phtml';
    }

    /**
     * Verify command result
     */
    public function verify()
    {
        $modulePath = $this->getApplication()->getWorkingPath()
            . DS . 'application'
            . DS . 'modules'
            . DS . $this->getOption('module');
        $paths = [
            $modulePath,
            $modulePath . DS . 'controllers',
            $modulePath . DS . 'controllers' . DS . $this->getOption('name') . '.php',
            $modulePath . DS . 'views',
            $modulePath . DS . 'views' . DS . $this->getOption('name') . '.phtml',

        ];
        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                return false;
            }
        }

        return true;
    }
}
