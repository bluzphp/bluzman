<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command;

use Bluz\Validator\Validator as v;
use Bluzman\Application\Application;
use Bluzman\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AbstractCommand
 * @package Bluzman\Command
 *
 * @method Application getApplication()
 *
 * @author Pavel Machekhin
 * @created 2013-11-28 15:47
 */
abstract class AbstractCommand extends Console\Command\Command
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

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
        if (!$this->fs) {
            $this->fs = new Filesystem();
        }
        return $this->fs;
    }

    /**
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return void
     */
    final public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->setInput($input);
        $this->setOutput($output);

        putenv('BLUZ_ENV=' . $input->getOption('env'));
    }

    /**
     * @param $message
     * @return void
     */
    public function write($message)
    {
        $this->getOutput()->writeln($message);
    }

    /**
     * @param $message
     * @return void
     */
    public function info($message)
    {
        $this->write("<info>$message</info>");
    }

    /**
     * @param $message
     * @return void
     */
    public function comment($message)
    {
        $this->write("<comment>$message</comment>");
    }

    /**
     * @param $message
     * @return void
     */
    public function question($message)
    {
        $this->write("<question>$message</question>:");
    }

    /**
     * @param $message
     * @return void
     */
    public function error($message)
    {
        $this->write("<error>$message</error>");
    }

    /**
     * @internal param $output
     */
    public function callForContribute()
    {
        $this->info(
            ' This command is not implemented yet. Don\'t be indifferent - you can contribute!' .
            ' https://github.com/bluzphp/bluzman. '
        );
    }

    /**
     * addModuleArgument
     *
     * @param int $required
     * @return AbstractCommand
     */
    protected function addModuleArgument($required = InputArgument::REQUIRED)
    {
        $module = new InputArgument('module', $required, 'Module name is required');
        $module->setValidator(
            v::string()->alphaNumeric('-_')->noWhitespace()
        );

        $this->getDefinition()->addArgument($module);

        return $this;
    }

    /**
     * addControllerArgument
     *
     * @param int $required
     * @return AbstractCommand
     */
    protected function addControllerArgument($required = InputArgument::REQUIRED)
    {
        $controller = new InputArgument(
            'controller',
            $required,
            'Controller name is required'
        );
        $controller->setValidator(
            v::string()->alphaNumeric('-_')->noWhitespace()
        );

        $this->getDefinition()->addArgument($controller);

        return $this;
    }
}
