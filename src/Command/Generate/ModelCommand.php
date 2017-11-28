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
class ModelCommand extends AbstractGenerateCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:model')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a new model')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate models files')
        ;

        $this->addModelArgument();
        $this->addTableArgument();
        $this->addForceOption();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $this->write('Running <info>generate:model</info> command');
        try {
            // validate
            $this->validateModelArgument();
            $this->validateTableArgument();

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
        $table = $input->getArgument('table');

        $data = [
            'model' => $model,
            'table' => $table,
            'primaryKey' => $this->getPrimaryKey($table),
            'columns' => $this->getColumns($table)
        ];

        /*
        if ($this->getApplication()->isModelExists($modelName)) {
            $helper = $this->getHelperSet()->get("question");
            $question = new ConfirmationQuestion(
                "\n<question>Model $modelName would be overwritten. y/N?</question>:\n> ",
                false
            );

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }
        */
        // generate table
        $tableFile = $this->getApplication()->getModelPath($model) . DS . 'Table.php';
        $this->generateFile('TableTemplate', $tableFile, $data);

        // generate row
        $rowFile = $this->getApplication()->getModelPath($model) . DS . 'Row.php';
        $this->generateFile('RowTemplate', $rowFile, $data);
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
        $modelPath = $this->getApplication()->getModelPath($model);

        $paths = [
            $modelPath . DS . 'Table.php',
            $modelPath . DS . 'Row.php'
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }

        $this->write(" |> Model <info>{$model}</info> has been successfully created.");
    }
}
