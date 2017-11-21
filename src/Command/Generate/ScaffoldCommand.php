<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Generator\GeneratorException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate Module Structure
 *
 * @package  Bluzman\Command\Generate
 */
class ScaffoldCommand extends AbstractGenerateCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:scaffold')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a new model and module with crud and grid')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate a scaffolding')
        ;

        $this->addModelArgument();
        $this->addTableArgument();
        $this->addModuleArgument();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->write('Running <info>generate:scaffold</info> command');
        try {
            // generate
            $this->runGenerateModel();
            $this->runGenerateModule();
            $this->runGenerateCrud();
            $this->runGenerateGrid();

            // verify it
            $this->verify($input, $output);

        } catch (\Exception $e) {
            $this->error("ERROR: {$e->getMessage()}");
        }
    }

    /**
     * Generate Model
     *
     * @return void
     */
    protected function runGenerateModel() : void
    {
        $command = $this->getApplication()->find('generate:model');

        $arguments = [
            'command' => 'generate:model',
            'model' => $this->getInput()->getArgument('model'),
            'table' => $this->getInput()->getArgument('table')
        ];
        $command->run(
            new ArrayInput($arguments),
            $this->getOutput()
        );
    }

    /**
     * Generate Module
     *
     * @return void
     */
    protected function runGenerateModule() : void
    {
        $command = $this->getApplication()->find('generate:module');

        $arguments = [
            'command' => 'generate:module',
            'module' => $this->getInput()->getArgument('module')
        ];
        $command->run(
            new ArrayInput($arguments),
            $this->getOutput()
        );
    }

    /**
     * Generate Crud
     *
     * @return void
     */
    protected function runGenerateCrud() : void
    {
        $command = $this->getApplication()->find('generate:crud');

        $arguments = [
            'command' => 'generate:crud',
            'model' => $this->getInput()->getArgument('model'),
            'module' => $this->getInput()->getArgument('module')
        ];
        $command->run(
            new ArrayInput($arguments),
            $this->getOutput()
        );
    }

    /**
     * Generate Grid
     *
     * @return void
     */
    protected function runGenerateGrid() : void
    {
        $command = $this->getApplication()->find('generate:grid');

        $arguments = [
            'command' => 'generate:grid',
            'model' => $this->getInput()->getArgument('model'),
            'module' => $this->getInput()->getArgument('module')
        ];
        $command->run(
            new ArrayInput($arguments),
            $this->getOutput()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws GeneratorException
     */
    public function verify(InputInterface $input, OutputInterface $output) : void
    {
        $model = $input->getArgument('model');
        $module = $input->getArgument('module');

        $modelPath = $this->getApplication()->getModelPath($model);
        $modulePath = $this->getApplication()->getModulePath($module);

        $paths = [
            $modelPath . DS . 'Crud.php',
            $modelPath . DS . 'Grid.php',
            $modelPath . DS . 'Row.php',
            $modelPath . DS . 'Table.php',
            $modulePath . DS . 'controllers' . DS . 'crud.php',
            $modulePath . DS . 'controllers' . DS . 'grid.php',
            $modulePath . DS . 'views' . DS . 'crud.phtml',
            $modulePath . DS . 'views' . DS . 'grid.phtml',
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new GeneratorException("File `$path` is not exists");
            }
        }

        $this->write("Scaffolding for <info>{$model}</info> has been successfully created.");
    }
}
