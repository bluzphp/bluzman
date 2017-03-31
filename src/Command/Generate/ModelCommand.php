<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluz\Proxy\Db;
use Bluz\Validator\Validator as v;
use Bluzman\Input\InputArgument;
use Bluzman\Input\InputException;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * ModelCommand
 *
 * @package  Bluzman\Command
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

        $model = new InputArgument('model', InputArgument::REQUIRED, 'Model name is required');
        $model->setValidator(
            v::string()->alphaNumeric()->noWhitespace()
        );

        $this->getDefinition()->addArgument($model);

        $table = new InputArgument('table', InputArgument::REQUIRED, 'Table name is required');
        $table->setValidator(
            v::string()->alphaNumeric('_')->noWhitespace()
        );

        $this->getDefinition()->addArgument($table);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->write("Running <info>generate:model</info> command");

            $model = $input->getArgument('model');
            $this->getDefinition()->getArgument('model')->validate($model);

            $table = $input->getArgument('table');
            $this->getDefinition()->getArgument('table')->validate($table);

            // generate directories and files
            $this->generate($input, $output);

            // verify it
            $this->verify($input, $output);

            $this->write("Model <info>{$model}</info> has been successfully created.");

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
        $tableName = $input->getArgument('table');

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

        // generate table
        $template = new Generator\Template\TableTemplate();
        $template->setFilePath($this->getApplication()->getModelPath($modelName) .DS. 'Table.php');
        $template->setTemplateData([
            'name' => $modelName,
            'table' => $tableName,
            'primaryKey' => $this->getPrimaryKey($tableName)
        ]);

        $generator = new Generator\Generator($template);
        $generator->make();

        // generate row
        $template = new Generator\Template\RowTemplate();
        $template->setFilePath($this->getApplication()->getModelPath($modelName) .DS. 'Row.php');
        $template->setTemplateData([
            'name' => $modelName,
            'table' => $tableName,
            'columns' => $this->getColumns($tableName)
        ]);

        $generator = new Generator\Generator($template);
        $generator->make();
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
            $modelPath . DS . 'Table.php',
            $modelPath . DS . 'Row.php'
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                throw new Generator\GeneratorException("File `$path` is not exists");
            }
        }
    }

    /**
     * @todo move it to DB class
     * @return array
     */
    protected function getPrimaryKey($table)
    {
        $connect = Db::getOption('connect');

        return Db::fetchColumn(
            '
            SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?
            ',
            [$connect['name'], $table, 'PRIMARY']
        );
    }

    /**
     * @todo move it to DB class
     * @return array
     */
    protected function getColumns($table)
    {
        $connect = Db::getOption('connect');

        return Db::fetchAll(
            '
            SELECT 
              COLUMN_NAME as name,
              COLUMN_TYPE as type
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
            ',
            [$connect['name'], $table]
        );
    }
}
