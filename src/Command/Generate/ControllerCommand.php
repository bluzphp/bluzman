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
 * @package  Bluzman\Command
 */
class ControllerCommand extends AbstractGenerateCommand
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
            ->setHelp('This command allows you to generate controller files')
        ;

        $this
            ->addModuleArgument()
            ->addControllerArgument()
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
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

            // generate directories and files
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);

            $this->write(
                "Controller <info>{$controller}</info> has been successfully created " .
                "in the module <info>{$controller}</info>."
            );
        } catch (\Exception $e) {
            $this->error("ERROR: {$e->getMessage()}");
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return $this
     */
    protected function generate(InputInterface $input, OutputInterface $output)
    {
        $module = $input->getArgument('module');
        $controller = $input->getArgument('controller');

        $template = new Generator\Template\ControllerTemplate;
        $template->setFilePath($this->getControllerPath($module, $controller));

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
    protected function getControllerPath($module, $controller)
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Bluzman\Generator\GeneratorException
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
                throw new Generator\GeneratorException("File or directory `$path` is not exists");
            }
        }
    }
}
