<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluzman\Input\InputException;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ModelCommand
 *
 * @package  Bluzman\Command\Generate
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->write('Running <info>generate:rest</info> command');
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
        $data = [
            'model' => $model,
            'module' => $module
        ];

        $crudPath = $this->getApplication()->getModelPath($model) . DS . 'Crud.php';
        if (!is_file($crudPath)) {
            throw new InputException(
                "CRUD for $model is not exist, " .
                "run command <question>bluzman generate:crud $model</question> before"
            );
        }

        $this->write(" |> Generate <info>$module/controllers/rest.php</info>");

        $controllerFile = $this->getControllerPath($module, 'rest');
        $this->generateFile('RestTemplate', $controllerFile, $data);
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

        $modulePath = $this->getApplication()->getModulePath($module);

        $paths = [
            $modulePath . DS . 'controllers' . DS . 'rest.php',
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }

        $this->write(" |> REST for <info>{$model}</info> has been successfully created.");
        $this->write(
            " |> <options=bold>Open page <info>/acl</info> in your browser ".
            "and set permissions <info>Management</info> for <info>{$module}</info> module</>"
        );
    }
}
