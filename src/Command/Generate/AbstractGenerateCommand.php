<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Bluz\Db\Table;
use Bluz\Proxy\Db;
use Bluz\Validator\Validator;
use Bluzman\Command\AbstractCommand;
use Bluzman\Generator\Generator;
use Bluzman\Generator\GeneratorException;
use Bluzman\Input\InputArgument;
use Bluzman\Input\InputException;
use Bluzman\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractCommand
 *
 * @package  Bluzman\Command\Generate
 */
abstract class AbstractGenerateCommand extends AbstractCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    abstract public function verify(InputInterface $input, OutputInterface $output): void;

    /**
     * Add Force Option
     *
     * @return void
     */
    protected function addForceOption(): void
    {
        $force = new InputOption('--force', '-f', InputOption::VALUE_NONE, 'Rewrite previously generated files');
        $this->getDefinition()->addOption($force);
    }

    /**
     * Add Model Argument
     *
     * @return void
     */
    protected function addModelArgument(): void
    {
        $model = new InputArgument('model', InputArgument::REQUIRED, 'Model name is required');
        $this->getDefinition()->addArgument($model);
    }

    /**
     * Validate Model Argument
     *
     * @return void
     * @throws InputException
     */
    protected function validateModelArgument(): void
    {
        $model = $this->getInput()->getArgument('model');

        $validator = Validator::create()
            ->string()
            ->alphaNumeric()
            ->noWhitespace();

        if (
            $this->getDefinition()->getArgument('model')->isRequired()
            && !$validator->validate($model)
        ) {
            throw new InputException($validator->getError());
        }
    }

    /**
     * Add Table Argument
     *
     * @return void
     */
    protected function addTableArgument(): void
    {
        $table = new InputArgument('table', InputArgument::REQUIRED, 'Table name is required');
        $this->getDefinition()->addArgument($table);
    }

    /**
     * Validate Table Argument
     *
     * @return void
     * @throws InputException
     */
    protected function validateTableArgument(): void
    {
        $table = $this->getInput()->getArgument('table');

        $validator = Validator::create()
            ->string()
            ->alphaNumeric('_')
            ->noWhitespace();

        if (
            $this->getDefinition()->getArgument('table')->isRequired()
            && !$validator->validate($table)
        ) {
            throw new InputException($validator->getError());
        }
    }

    /**
     * Get Table instance
     *
     * @param string $model
     *
     * @return Table
     * @throws GeneratorException
     */
    protected function getTableInstance(string $model): Table
    {
        $file = $this->getApplication()->getModelPath($model) . DS . 'Table.php';
        if (!file_exists($file)) {
            throw new GeneratorException(
                "Model $model is not exist, run command `bluzman generate:model $model` before"
            );
        }
        include_once $file;
        $class = '\\Application\\' . ucfirst($model) . '\\Table';
        if (!class_exists($class)) {
            throw new GeneratorException("Bluzman can't found `Table` class for model `$model`");
        }
        return $class::getInstance();
    }

    /**
     * Required for correct mock it
     *
     * @param string $class
     * @return mixed
     */
    protected function getTemplate(string $class)
    {
        $class = '\\Bluzman\\Generator\\Template\\' . $class;
        return new $class();
    }

    /**
     * Small wrapper for simplify code
     *
     * @param  string $class
     * @param  string $file
     * @param  array  $data
     *
     * @return void
     */
    protected function generateFile(string $class, string $file, array $data = []): void
    {
        if (file_exists($file) && !$this->getInput()->getOption('force')) {
            $this->comment(" |> File <info>$file</info> already exists");
            return;
        }

        $template = $this->getTemplate($class);
        $template->setFilePath($file);
        $template->setTemplateData($data);

        $generator = new Generator($template);
        $generator->make();
    }

    /**
     * @param string $module
     * @param string $controller
     * @return string
     */
    protected function getControllerPath(string $module, string $controller): string
    {
        return $this->getApplication()->getModulePath($module)
            . DS . 'controllers'
            . DS . $controller
            . '.php';
    }

    /**
     * @param string $module
     * @param string $controller
     * @return string
     */
    protected function getViewPath(string $module, string $controller): string
    {
        return $this->getApplication()->getModulePath($module)
            . DS . 'views'
            . DS . $controller
            . '.phtml';
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
