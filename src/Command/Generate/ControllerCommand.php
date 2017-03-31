<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Generator;
use Bluzman\Input\InputException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ControllerCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 13:58
 */
class ControllerCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:controller')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a new controller')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate a controller files')
        ;

        $this
            ->addModuleArgument()
            ->addControllerArgument()
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws InputException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->write("Running <info>generate:controller</info> command");

            $module = $input->getArgument('module');
            $this->getDefinition()->getArgument('module')->validate($module);

            if (!$this->getApplication()->isModuleExists($module)) {
                throw new InputException(
                    "Module $module is not exist, ".
                    "run command <question>bluzman generate:module $module</question> before");
            }

            $controller = $input->getArgument('controller');
            $this->getDefinition()->getArgument('controller')->validate($controller);


            $this->generate($module, $controller)
                ->verify($input, $output);

            $this->write(
                "Controller <info>{$input->getArgument('controller')}</info> has been successfully created " .
                "in the module <info>{$input->getArgument('controller')}</info>."
            );
        } catch (InputException $e) {
            $this->error("ERROR: {$e->getMessage()}");
        }
    }

    /**
     * @param  string $module
     * @param  string $controller
     * @return $this
     */
    protected function generate($module, $controller)
    {
        $template = new Generator\Template\ControllerTemplate;
        $template->setFilePath($this->getFilePath($module, $controller));

        $generator = new Generator\Generator($template);
        $generator->make();

        $template = new Generator\Template\ViewTemplate;
        $template->setFilePath($this->getViewPath($module, $controller));
        $template->setTemplateData(['name' => $controller]);

        $generator = new Generator\Generator($template);
        $generator->make();

        return $this;
    }

    /**
     * @return string
     * @throws InputException
     */
    protected function getFilePath($module, $controller)
    {
        return $this->getApplication()->getModulePath($module)
            . DS . 'controllers'
            . DS . $controller
            . '.php';
    }

    /**
     * @return string
     * @throws InputException
     */
    protected function getViewPath($module, $controller)
    {
        return $this->getApplication()->getModulePath($module)
            . DS . 'views'
            . DS . $controller
            . '.phtml';
    }

    /**
     * Verify command result
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $modulePath = $this->getApplication()->getModulePath($input->getArgument('module'));

        $paths = [
            $modulePath,
            $modulePath . DS . 'controllers',
            $modulePath . DS . 'controllers' . DS . $input->getArgument('controller') . '.php',
            $modulePath . DS . 'views',
            $modulePath . DS . 'views' . DS . $input->getArgument('controller') . '.phtml',

        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                return false;
            }
        }

        return true;
    }
}
