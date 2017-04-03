<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluz\Validator\Validator as v;
use Bluzman\Input\InputArgument;
use Bluzman\Input\InputException;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ModelCommand
 *
 * @package  Bluzman\Command
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

        $module = new InputArgument(
            'module',
            InputArgument::OPTIONAL,
            'Module name, if you need to generate `view` controller and view'
        );

        $module->setValidator(
            v::string()->alphaNumeric('-_')->noWhitespace()
        );


        $this->getDefinition()->addArgument($module);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->write("Running <info>generate:crud</info> command");

            $model = $input->getArgument('model');
            $this->getDefinition()->getArgument('model')->validate($model);

            if (!$this->getApplication()->isModelExists($model)) {
                throw new InputException(
                    "Model $model is not exist, ".
                    "run command <question>bluzman generate:model $model</question> before");
            }

            if ($module = $input->getArgument('module')) {
                $this->getDefinition()->getArgument('module')->validate($module);

                if (!$this->getApplication()->isModuleExists($module)) {
                    throw new InputException(
                        "Module $module is not exist, ".
                        "run command <question>bluzman generate:module $module</question> before");
                }
            }

            // generate directories and files
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);

            $this->write("CRUD for <info>{$model}</info> has been successfully created.");

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
    protected function generate(InputInterface $input, OutputInterface $output)
    {
        $modelName = ucfirst($input->getArgument('model'));

        // generate CRUD
        $crudFile = $this->getApplication()->getModelPath($modelName) .DS. 'Crud.php';

        if (file_exists($crudFile)) {
            $this->comment("Crud file <info>$modelName/Crud.php</info> already exists");
        } else {
            $template = $this->getTemplate('CrudTemplate');
            $template->setFilePath($crudFile);
            $template->setTemplateData([
                'name' => $modelName
            ]);

            $generator = new Generator\Generator($template);
            $generator->make();
        }

        if ($module = $input->getArgument('module')) {
            $this->write("Generate <info>$module/controllers/crud.php</info>");

            $controllerFile = $this->getControllerPath($module, 'crud');
            if (file_exists($controllerFile)) {
                $this->comment("Controller file <info>$module/crud</info> already exists");
            } else {
                $template = new Generator\Template\CrudControllerTemplate();
                $template->setFilePath($controllerFile);
                $template->setTemplateData(['name' => $modelName]);

                $generator = new Generator\Generator($template);
                $generator->make();
            }

            $this->write("Generate <info>$module/views/crud.phtml</info>");

            $viewFile = $this->getViewPath($module, 'crud');
            if (file_exists($viewFile)) {
                $this->comment("View file <info>$module/crud</info> already exists");
            } else {
                $template = new Generator\Template\CrudViewTemplate();
                $template->setFilePath($viewFile);
                $template->setTemplateData(['name' => $modelName]);

                $generator = new Generator\Generator($template);
                $generator->make();
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Bluzman\Generator\GeneratorException
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $modelPath = $this->getApplication()->getModelPath($input->getArgument('model'));

        $paths = [
            $modelPath . DS . 'Crud.php',
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }
    }
}
