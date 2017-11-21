<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ControllerCommand
 *
 * @package  Bluzman\Command\Generate
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

        $this->addModuleArgument();
        $this->addControllerArgument();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->write('Running <info>generate:controller</info> command');
        try {
            // validate
            $this->validateModuleArgument();
            $this->validateControllerArgument();

            // generate directories and files
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);
        } catch (\Exception $e) {
            $this->error("ERROR: {$e->getMessage()}");
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function generate(InputInterface $input, OutputInterface $output) : void
    {
        $module = $input->getArgument('module');
        $controller = $input->getArgument('controller');

        $controllerFile = $this->getControllerPath($module, $controller);
        $this->generateFile('ControllerTemplate', $controllerFile);

        $viewFile = $this->getViewPath($module, $controller);
        $this->generateFile('ViewTemplate', $viewFile, ['name' => $controller]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Bluzman\Generator\GeneratorException
     */
    public function verify(InputInterface $input, OutputInterface $output) : void
    {
        $module = $input->getArgument('module');
        $controller = $input->getArgument('controller');

        $modulePath = $this->getApplication()->getModulePath($module);

        $paths = [
            $modulePath,
            $modulePath . DS . 'controllers',
            $modulePath . DS . 'controllers' . DS . $controller . '.php',
            $modulePath . DS . 'views',
            $modulePath . DS . 'views' . DS . $controller . '.phtml',

        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File or directory `$path` is not exists");
            }
        }

        $this->write(
            " |> Controller <info>{$controller}</info> has been successfully created " .
            "in the module <info>{$module}</info>."
        );
    }
}
