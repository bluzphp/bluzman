<?php

namespace Bluzman\Command\Init;

use Bluzman\Command;
use Bluzman\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Respect\Validation\Validator as v;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ModelCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 1:58 PM
 */

class ModelCommand extends Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $name = 'init:model';

    /**
     * @var string
     */
    protected $description = 'Initialize a new model';

    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_OPTIONAL, ' name of model.', null, v::alnum('-')->noWhitespace()],
            ['table', null, InputOption::VALUE_OPTIONAL, ' name of table.', null, v::alnum('-')->noWhitespace()]
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Bluzman\Input\InputException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->info("Running \"init:model\" command"));

        $this->generate()->verify();

        $output->writeln("Model \"" . $this->info($this->getOption('name')) . "\"" .
            " has been successfully created in the model \"" . $this->info($this->getOption('name')) . "\".");
    }

    /**
     * @param $controllerName
     * @param $moduleName
     */
    protected function generate()
    {
        $primaryKey = $this->getPrimaryKey($this->getOption('table'));
        $columns = $this->getColumns($this->getOption('table'));

        // generate row
        $template = new Generator\Template\TableTemplate;
        $template->setFilePath($this->getFilePath(). DIRECTORY_SEPARATOR .'Table.php');
        $data = [
            'name' => $this->getOption('name'),
            'table' => $this->getOption('table'),
            'primaryKey' => $primaryKey
        ];
        $template->setTemplateData($data);

        $generator = new Generator\Generator($template);
        $generator->make();

        // generate table
        $template = new Generator\Template\RowTemplate;
        $template->setFilePath($this->getFilePath(). DIRECTORY_SEPARATOR .'Row.php');
        unset($data);
        $data = [
            'name' => $this->getOption('name'),
            'table' => $this->getOption('table'),
            "columns" => $columns
        ];
        $template->setTemplateData($data);

        $generator = new Generator\Generator($template);
        $generator->make();

        return $this;
    }

    /**
     * @return string
     */
    protected function getFilePath()
    {
        $modelPath = $this->getApplication()->getWorkingPath() . DIRECTORY_SEPARATOR
            . "application" . DIRECTORY_SEPARATOR
            . "models";

        if (!is_dir($modelPath)) {
            mkdir($modelPath, 0755);
        }

        $path = $modelPath . DIRECTORY_SEPARATOR . $this->getOption('name');

        if (!is_dir($path)) {
            mkdir($path, 0755);
        }

        return $path;
    }

    /**
     * Verify command result
     */
    public function verify()
    {
        $fs = new Filesystem();

        if (!$fs->exists($this->getFilePath())) {
            throw new \RuntimeException("Something is wrong. Controller was not created");
        }

        return true;
    }

    protected function getPrimaryKey()
    {
        $table = $this->getOption('table');

        $dbh = $this->getApplication()->getDbConnection();

        $dbh->prepare("DESCRIBE $table");

        $q = $dbh->prepare("SHOW KEYS FROM $table WHERE Key_name = \"PRIMARY\"");

        $q->execute();
        $keys = $q->fetchAll(\PDO::FETCH_ASSOC);

        $primaryKeys = array();

        foreach ($keys as $key) {
            $primaryKeys[] = $key["Column_name"];
        }

        return $primaryKeys;
    }

    protected function getColumns()
    {
        $dbh = $this->getApplication()->getDbConnection();
        $columns = array();
        $name = $this->getOption('name');

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

}