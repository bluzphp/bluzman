<?php

namespace Bluzman\Command\Init;

use Bluzman\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * ModuleCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  4/05/13 09:57 PM
 */

class ModuleCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('init:module')
            ->setDescription('Initialize a new module')
            ->addArgument(
                'moduleName',
                InputArgument::OPTIONAL,
                'New module name'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running "init:module" command</info>');

        $this->verify($input, $output);

        $name = $input->getArgument('moduleName');

        $path = $this->getApplication()->getModulePath($name);

        mkdir($path, 0755);

        $this->addSubFolders($path, array('controllers', 'views'));

        $output->writeln('');

        $output->writeln('Module <info>"' . $name . '"</info> has been successfully created.');
    }

    /**
     * Ask and validate the name argument
     *
     * @param $input
     * @param $output
     * @return mixed
     */
    protected function askName($input, $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $output->writeln('');

        return $dialog->askAndValidate(
            $output,
            "<question>Please enter the name of the module:</question> \n> ",
            function ($name) use ($input, $output, $dialog) {
                return $this->validateModuleName($name, $input, $output);
            },
            true
        );
    }

    /**
     * Validate the module
     *
     * @param $name
     * @param $input
     * @param $output
     * @return mixed
     * @throws \RuntimeException
     */
    protected function validateModuleName($name, $input, $output)
    {
        if (!$input->isInteractive()) {
            throw new \RuntimeException('Command should be executed in interactive mode.');
        }

        if (empty($name)) {
            $output->writeln('');
            $output->writeln('<error>ERROR: Please enter a correct name of the module</error>');

            return $this->askName($input, $output);
        }

        if ($this->getApplication()->isModuleExists($name)) {
            $output->writeln('');
            $output->writeln('<error>ERROR: Module with name ' . $name . ' is already exist.</error>');
            $output->writeln('');
            exit();

            return $this->askName($input, $output);
        }

        $input->setArgument('moduleName', $name);

        return $name;
    }

    /**
     *
     *
     * @param $path
     * @param array $subfolders
     */
    protected function addSubFolders($path, array $subfolders = array())
    {
        foreach ($subfolders as $subfolderName) {
            $subfolderPath = $path . DIRECTORY_SEPARATOR . $subfolderName;

            mkdir($subfolderPath, 0755);
            shell_exec('touch ' . $subfolderPath . DIRECTORY_SEPARATOR . '.gitkeep');
        }
    }

    /**
     * @todo
     *
     * @return bool
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('moduleName');

        if (empty($name)) {
            $this->askName($input, $output);
        } else {
            $this->validateModuleName($input->getArgument('moduleName'), $input, $output);
        }
    }
}