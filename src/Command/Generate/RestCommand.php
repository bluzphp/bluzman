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
class RestCommand extends AbstractGenerateCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:rest')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a REST controller')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate REST controller')
        ;

        $this->addModelArgument();
        $this->addModuleArgument();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->write("Running <info>generate:rest</info> command");

            $model = $input->getArgument('model');
            $this->getDefinition()->getArgument('model')->validate($model);

            if (!$this->getApplication()->isModelExists($model)) {
                throw new InputException(
                    "Model $model is not exist, ".
                    "run command <question>bluzman generate:model $model</question> before"
                );
            }

            $crudPath = $this->getApplication()->getModelPath($model) .DS. 'Crud.php';
            if (!is_file($crudPath)) {
                throw new InputException(
                    "CRUD for $model is not exist, ".
                    "run command <question>bluzman generate:crud $model</question> before"
                );
            }

            $module = $input->getArgument('module');
            $this->getDefinition()->getArgument('module')->validate($module);

            if (!$this->getApplication()->isModuleExists($module)) {
                throw new InputException(
                    "Module $module is not exist, ".
                    "run command <question>bluzman generate:module $module</question> before"
                );
            }

            // generate directories and files
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);

            $this->write("REST for <info>{$model}</info> has been successfully created.");
            $this->write("Open page <info>/acl</info> in your browser and set permissions for <info>{$module}</info>");
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
        $model= ucfirst($input->getArgument('model'));
        $module= $input->getArgument('module');

        $this->write("Generate <info>$module/controllers/rest.php</info>");

        $controllerFile = $this->getControllerPath($module, 'rest');
        if (file_exists($controllerFile)) {
            $this->comment("Controller file <info>$module/crud</info> already exists");
        } else {
            $template = $this->getTemplate('RestTemplate');
            $template->setFilePath($controllerFile);
            $template->setTemplateData(['model' => $model]);

            $generator = new Generator\Generator($template);
            $generator->make();
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
        $modulePath = $this->getApplication()->getModulePath($input->getArgument('module'));

        $paths = [
            $modulePath . DS . 'controllers' . DS . 'rest.php',
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }
    }
}
