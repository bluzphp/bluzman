<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluz\Validator\Validator as v;
use Bluzman\Command\AbstractCommand;
use Bluzman\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractCommand
 *
 * @package  Bluzman\Command\Generate
 */
abstract class AbstractGenerateCommand extends AbstractCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract public function verify(InputInterface $input, OutputInterface $output);

    /**
     * addModelArguments
     *
     * @return self
     */
    protected function addModelArgument()
    {
        $model = new InputArgument('model', InputArgument::REQUIRED, 'Model name is required');
        $model->setValidator(
            v::create()->string()->alphaNumeric()->noWhitespace()
        );

        $this->getDefinition()->addArgument($model);

        return $this;
    }

    /**
     * Required for correct mock it
     *
     * @param  string $class
     * @return mixed
     */
    protected function getTemplate($class)
    {
        $class = '\\Bluzman\\Generator\\Template\\' . $class;
        return new $class;
    }

    /**
     * @return string
     */
    protected function getControllerPath($module, $controller)
    {
        return $this->getApplication()->getModulePath($module)
            . DS . 'controllers'
            . DS . $controller
            . '.php';
    }

    /**
     * @return string
     */
    protected function getViewPath($module, $controller)
    {
        return $this->getApplication()->getModulePath($module)
            . DS . 'views'
            . DS . $controller
            . '.phtml';
    }
}
