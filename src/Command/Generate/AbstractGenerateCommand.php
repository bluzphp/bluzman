<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluz\Validator\Validator as v;
use Bluzman\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractCommand
 *
 * @package  Bluzman\Command
 */
abstract class AbstractGenerateCommand extends \Bluzman\Command\AbstractCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract public function verify(InputInterface $input, OutputInterface $output);

    /**
     * addModuleArgument
     *
     * @return self
     */
    protected function addModuleArgument()
    {
        $module = new InputArgument('module', InputArgument::REQUIRED, 'Module name is required');
        $module->setValidator(
            v::string()->alphaNumeric('-_')->noWhitespace()
        );

        $this->getDefinition()->addArgument($module);

        return $this;
    }

    /**
     * addControllerArgument
     *
     * @return self
     */
    protected function addControllerArgument()
    {
        $controller = new InputArgument(
            'controller',
            InputArgument::REQUIRED,
            'Controller name is required'
        );
        $controller->setValidator(
            v::string()->alphaNumeric('-_')->noWhitespace()
        );

        $this->getDefinition()->addArgument($controller);

        return $this;
    }
}
