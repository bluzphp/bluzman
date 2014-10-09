<?php

namespace Bluzman\Command\Init;

use Bluzman\Command\Command;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * ModelCommand
 *
 * @todo Add an ability to define a namespace of model,
 *       so it will be possible to have classes like Application/Users/Profiles/Table
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 2:00 PM
 */

class ModelCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName("init:model")
            ->setDescription("Initialize a new model")
            ->addArgument(
                "name",
                InputArgument::OPTIONAL,
                "The name of model. In case of empty `table` argument it is also a name of database table."
            )
            ->addArgument(
                "table",
                InputArgument::OPTIONAL,
                "The name of DB table."
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Running \"init:model\" command</info>");

        try {
            $this->verify($input, $output);

            $name = $input->getArgument("name");
            $table = $input->getArgument("table");

            $this->generate($name, $table, $input, $output);

            $output->writeln("\nModel <info>\"" . $name . "\"</info> has been successfully created</info>.");

        } catch (\LogicException $e) {
            throw new \RuntimeException($e->getMessage());
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException("Some error occurred.");
        }
    }

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    protected function askModelName(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get("dialog");

        // ask user enter a valid name of new model
        return $dialog->askAndValidate(
            $output,
            "\n<question>Please enter the name of the model:</question> \n> ",
            function ($name) use ($input, $output, $dialog) {
                return $this->validateModelName($name, $input, $output);
            },
            true
        );
    }

    /**
     * @param $name
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function validateModelName($name, InputInterface $input, OutputInterface $output)
    {
        if (empty($name)) {
            $output->writeln("\n<error>ERROR: Please enter a correct name of the model</error>");

            return $this->askModelName($input, $output);
        }

        $input->setArgument("name", $name);

        return $name;
    }

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    protected function askTableName(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get("dialog");

        // ask user enter a valid name of new model
        return $dialog->askAndValidate(
            $output,
            "\n<question>Please enter correct name of the table:</question> \n> ",
            function ($table) use ($input, $output, $dialog) {
                return $this->validateTable($table, $input, $output);
            },
            true
        );
    }

    /**
     * @param $table
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function validateTable($table, InputInterface $input, OutputInterface $output)
    {
        if (empty($table)) {
            $output->writeln("\n<error>ERROR: Please enter a correct name of the table</error>");

            return $this->askTableName($input, $output);
        }

        $input->setArgument("table", $table);

        return $table;
    }

    /**
     * Get primary key of table
     *
     * @param $table
     * @return array
     */
    protected function getPrimaryKey($table)
    {
        $dbh = $this->getApplication()->getDbConnection();

        $dbh->prepare("DESCRIBE $table");

        $q = $dbh->prepare('SHOW KEYS FROM $name WHERE Key_name = "PRIMARY"');

        $q->execute();
        $keys = $q->fetchAll(\PDO::FETCH_ASSOC);

        $primaryKeys = array();

        foreach ($keys as $key) {
            $primaryKeys[] = $key["Column_name"];
        }

        return $primaryKeys;
    }

    /**
     * Get list of table columns
     *
     * @param $name
     * @return array
     */
    protected function getColumns($name)
    {
        $dbh = $this->getApplication()->getDbConnection();
        $columns = array();

        $q = $dbh->prepare("DESCRIBE $name");
        $q->execute();

        foreach($q->fetchAll(\PDO::FETCH_ASSOC) as $column) {
            $type = preg_replace("/\(.*/s", "", $column["Type"]);

            $columns[] = array(
                "name" => $column["Field"],
                "type" => $type
            );
        };

        return $columns;
    }

    /**
     * Validate and generate a model
     *
     * @param $controllerName
     * @param $moduleName
     */
    protected function generate($modelName, $table, InputInterface $input, OutputInterface $output)
    {
        $generator = new Generator\Generator();

        $primaryKey = $this->getPrimaryKey($table);
        $columns = $this->getColumns($table);

        try {
            // generate table
            $arguments = array(
                "Table.php",
                $this->getPath($modelName),
                Generator\Generator::ENTITY_TYPE_MODEL_TABLE,
                array(
                    "name" => $modelName,
                    "table" => $table,
                    "primaryKey" => $primaryKey
                )
            );

            call_user_func_array(array($generator, "generateTemplate"), $arguments);

        } catch (Generator\Template\Exception\AlreadyExistsException $e) {
            $dialog = $this->getHelperSet()->get("dialog");

            $result = $dialog->askConfirmation(
                $output,
                "\n<question>Model " . $e->getMessage() . " would be overwritten. y/N?:</question>\n> ",
                false
            );

            if ($result) {
                $arguments[] = true; // rewrite argument

                call_user_func_array(array($generator, "generateTemplate"), $arguments);
            }
        }

        try {
            // generate row
            $arguments = array(
                "Row.php",
                $this->getPath($modelName),
                Generator\Generator::ENTITY_TYPE_MODEL_ROW,
                array(
                    "name" => $modelName,
                    "table" => $table,
                    "columns" => $columns
                )
            );

            call_user_func_array(array($generator, "generateTemplate"), $arguments);

        } catch (Generator\Template\Exception\AlreadyExistsException $e) {
            $dialog = $this->getHelperSet()->get("dialog");

            $result = $dialog->askConfirmation(
                $output,
                "\n<question>Model " . $e->getMessage() . " would be overwritten. y/N?:</question>\n> ",
                false
            );

            if ($result) {
                $arguments[] = true; // rewrite argument

                call_user_func_array(array($generator, "generateTemplate"), $arguments);
            }
        }
    }

    /**
     * Get full path to the model
     *
     * @param $modelName
     * @return string
     */
    protected function getPath($modelName)
    {
        $modelPath = $this->getApplication()->getPath() . DIRECTORY_SEPARATOR
            . "application" . DIRECTORY_SEPARATOR
            . "models" . DIRECTORY_SEPARATOR
            . "Application";

        if (!is_dir($modelPath)) {
            mkdir($modelPath, 0755);
        }

        $path = $modelPath . DIRECTORY_SEPARATOR . $modelName;

        if (!is_dir($path)) {
            mkdir($path, 0755);
        }

        return $path;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        // command should be executed only in interactive mode
        if (!$input->isInteractive()) {
            throw new \RuntimeException("Command should be executed in interactive mode.");
        }

        $name = $input->getArgument("name");

        // `name` is required argument
        if (empty($name)) {
            $this->askModelName($input, $output);
        } else {
            $this->validateModelName($name, $input, $output);
        }

        $name = $input->getArgument("name");
        $table = $input->getArgument("table");

        if (empty($table)) {
            $table = strtolower($name);
            $dialog = $this->getHelperSet()->get("dialog");

            $result = $dialog->askConfirmation(
                $output,
                "\n<question>The name of database table would be \"" . $table . "\". Y/n?:</question>\n> ",
                true
            );

            if (!$result) {
                $this->askTableName($input, $output);
            } else {
                $input->setArgument('table', $table);
            }
        }

        $table = $input->getArgument("table");

        $this->validateTable($table, $input, $output);

        $input->setArgument("name", ucfirst($name));
        $input->setArgument("table", $table);
    }
}