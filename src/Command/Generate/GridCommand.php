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
class GridCommand extends AbstractGenerateCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:grid')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a GRID for model')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate GRID files')
        ;

        $this->addModelArgument();
        $this->addModuleArgument(InputArgument::OPTIONAL);
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
        $this->write('Running <info>generate:grid</info> command');
        try {
            // validate
            $this->validateModelArgument();
            $this->validateModuleArgument();

            // generate directories and files
            $this->generate($input, $output);

            // verify files
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

        if ($module) {
            // controller and view generators required the `Model\Table` class
            // validator is present on previous step
            $data['columns'] = $this->getTableInstance($model)::getMeta();
        }

        // generate GRID class
        $this->write(" |> Generate Grid class <info>$model\\Grid</info>");

        $gridFile = $this->getApplication()->getModelPath($model) . DS . 'Grid.php';
        $this->generateFile('GridTemplate', $gridFile, $data);

        if ($module) {
            $this->write(" |> Generate Grid controller <info>$module/controllers/grid.php</info>");

            $controllerFile = $this->getControllerPath($module, 'grid');
            $this->generateFile('GridControllerTemplate', $controllerFile, $data);

            $this->write(" |> Generate Grid view <info>$module/views/grid.phtml</info>");

            $viewFile = $this->getViewPath($module, 'grid');
            $this->generateFile('GridViewTemplate', $viewFile, $data);
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
            $modelPath . DS . 'Grid.php',
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }

        // notifications
        $this->write(" |> GRID for <info>{$model}</info> has been successfully created.");

        if ($module) {
            $this->write(
                " |> <options=bold>Open page <info>/acl</info> in your browser " .
                "and set permission <info>Management</info> for <info>{$module}</info> module</>"
            );
        }
    }
}
