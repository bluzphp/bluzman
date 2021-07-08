<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command;

use Bluz\Config\ConfigException;
use Bluz\Proxy\Config;
use Bluz\Validator\Validator;
use Bluzman\Application\Application;
use Bluzman\Input\InputArgument;
use Bluzman\Input\InputException;
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
    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
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
     * @param Filesystem $fs
     */
    public function setFs(Filesystem $fs): void
    {
        $this->fs = $fs;
    }

    /**
     * @return Filesystem
     */
    public function getFs(): Filesystem
    {
        if (!$this->fs) {
            $this->fs = new Filesystem();
        }
        return $this->fs;
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return void
     * @throws ConfigException
     */
    final public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->setInput($input);
        $this->setOutput($output);

        putenv('BLUZ_ENV=' . ($input->getOption('env') ?: getenv('BLUZ_ENV')));

        $loader = new \Bluz\Config\ConfigLoader();
        $loader->setPath(PATH_APPLICATION);
        $loader->setEnvironment($input->getOption('env'));
        $loader->load();

        $config = new \Bluz\Config\Config();
        $config->setFromArray($loader->getConfig());

        Config::setInstance($config);
    }

    /**
     * @param $message
     * @return void
     */
    public function write($message): void
    {
        $this->getOutput()->writeln($message);
    }

    /**
     * @param $message
     * @return void
     */
    public function info($message): void
    {
        $this->write("<info>$message</info>");
    }

    /**
     * @param $message
     * @return void
     */
    public function comment($message): void
    {
        $this->write("<comment>$message</comment>");
    }

    /**
     * @param $message
     * @return void
     */
    public function question($message): void
    {
        $this->write("<question>$message</question>:");
    }

    /**
     * @param $message
     * @return void
     */
    public function error($message): void
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
     * Add Module Argument
     *
     * @param int $required
     * @return void
     */
    protected function addModuleArgument($required = InputArgument::REQUIRED): void
    {
        $module = new InputArgument('module', $required, 'Module name is required');
        $this->getDefinition()->addArgument($module);
    }

    /**
     * Validate Module Argument
     *
     * @return void
     * @throws InputException
     */
    protected function validateModuleArgument(): void
    {
        $module = $this->getInput()->getArgument('module');

        $validator = Validator::create()
            ->string()
            ->alphaNumeric('-_')
            ->noWhitespace();

        if (
            $this->getDefinition()->getArgument('module')->isRequired()
            && !$validator->validate($module)
        ) {
            throw new InputException($validator->getError());
        }
    }

    /**
     * Add Controller Argument
     *
     * @param int $required
     * @return void
     */
    protected function addControllerArgument($required = InputArgument::REQUIRED): void
    {
        $controller = new InputArgument('controller', $required, 'Controller name is required');
        $this->getDefinition()->addArgument($controller);
    }

    /**
     * Validate Module Argument
     *
     * @return void
     * @throws InputException
     */
    protected function validateControllerArgument(): void
    {
        $controller = $this->getInput()->getArgument('controller');

        $validator = Validator::create()
            ->string()
            ->alphaNumeric('-_')
            ->noWhitespace();

        if (
            $this->getDefinition()->getArgument('controller')->isRequired()
            && !$validator->validate($controller)
        ) {
            throw new InputException($validator->getError());
        }
    }
}
