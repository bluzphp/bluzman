<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Input\InputArgument;
use Bluzman\Input\InputException;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ModelCommand
 *
 * @package  Bluzman\Command\Generate
 */
class CrudCommand extends AbstractGenerateCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:crud')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a CRUD for model')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate CRUD files')
        ;

        $this->addModelArgument();
        $this->addModuleArgument(InputArgument::OPTIONAL);
        $this->addForceOption();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Bluzman\Generator\GeneratorException
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->write('Running <info>generate:crud</info> command');
        try {
            // validate
            $this->validateModelArgument();
            $this->validateModuleArgument();

            // generate directories and files
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);
        } catch (InputException $e) {
            $this->error("ERROR: {$e->getMessage()}");
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws InputException
     */
    protected function generate(InputInterface $input, OutputInterface $output) : void
    {
        $model = ucfirst($input->getArgument('model'));
        $module = $input->getArgument('module');

        // template data
        $data = [
            'model' => $model,
            'module' => $module
        ];

        // generate CRUD class
        $this->write(" |> Generate CRUD class <info>$model\\Crud</info>");
        $crudFile = $this->getApplication()->getModelPath($model) . DS . 'Crud.php';
        $this->generateFile('CrudTemplate', $crudFile, $data);

        if ($module) {
            if (!$this->getApplication()->isModelExists($model)) {
                throw new InputException(
                    "Model $model is not exist, " .
                    "run command <question>bluzman generate:model $model</question> before"
                );
            }

            $this->write(" |> Generate CRUD controller <info>$module/controllers/crud.php</info>");

            $controllerFile = $this->getControllerPath($module, 'crud');
            $this->generateFile('CrudControllerTemplate', $controllerFile, $data);

            $this->write(" |> Generate CRUD view <info>$module/views/crud.phtml</info>");

            $tableInstance = $this->getTableInstance($model);
            $data['columns'] = $tableInstance::getMeta();

            $viewFile = $this->getViewPath($module, 'crud');
            $this->generateFile('CrudViewTemplate', $viewFile, $data);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Bluzman\Generator\GeneratorException
     */
    public function verify(InputInterface $input, OutputInterface $output) : void
    {
        $model = $input->getArgument('model');
        $module = $input->getArgument('module');

        $modelPath = $this->getApplication()->getModelPath($model);

        $paths = [
            $modelPath . DS . 'Crud.php',
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }

        $this->write(" |> CRUD for <info>{$model}</info> has been successfully created.");
        if ($module) {
            $this->write(
                " |> <options=bold>Open page <info>/acl</info> in your browser " .
                "and set permission <info>Management</info> for <info>{$module}</info> module</>"
            );
        }
    }
}
