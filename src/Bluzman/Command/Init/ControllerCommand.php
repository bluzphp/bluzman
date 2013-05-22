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
 * ControllerCommand
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 1:58 PM
 */

class ControllerCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('init:controller')
            ->setDescription('Initialize a new controller')
            ->addArgument(
                'moduleName',
                InputArgument::OPTIONAL,
                'Module name'
            )
            ->addArgument(
                'controllerName',
                InputArgument::OPTIONAL,
                'Controller name'
            );


    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Running "init:controller" command</info>');

        $this->verify($input, $output);

        $moduleName = $input->getArgument('moduleName');
        $controllerName = $input->getArgument('controllerName');

        if (!empty($moduleName) && !empty($controllerName)) {
            $this->generate($controllerName, $moduleName, $input, $output);

            $output->writeln('');
            $output->writeln('Controller <info>"' . $controllerName . '"</info> ' .
                'has been successfully created in module <info>"' . $moduleName . '"</info>.');
        }
    }

    /**
     * @param $controllerName
     * @param $moduleName
     */
    protected function generate($controllerName, $moduleName, InputInterface $input, OutputInterface $output)
    {
        $generator = new Generator\Generator();

        // generate controller
        try {
            $arguments = array(
                $controllerName . '.php',
                $this->getPath($moduleName, 'controllers'),
                Generator\Generator::ENTITY_TYPE_CONTROLLER
            );

            call_user_func_array(array($generator, 'generateTemplate'), $arguments);

        } catch (Generator\Template\Exception\AlreadyExistsException $e) {
            $dialog = $this->getHelperSet()->get('dialog');

            $result = $dialog->askConfirmation(
                $output,
                '<question>Controller ' . $e->getMessage() . ' would be overwritten. y/N?:</question> ',
                false
            );

            if ($result) {
                $arguments[] = array(); //options argument
                $arguments[] = true; // rewrite argument

                call_user_func_array(array($generator, 'generateTemplate'), $arguments);
            }
        }

        // generate view
        try {
            $arguments = array(
                $controllerName . '.phtml',
                $this->getPath($moduleName, 'views'),
                Generator\Generator::ENTITY_TYPE_VIEW,
                array(
                    'name' => $controllerName
                )
            );

            call_user_func_array(array($generator, 'generateTemplate'), $arguments);
        } catch (Generator\Template\Exception\AlreadyExistsException $e) {
            $dialog = $this->getHelperSet()->get('dialog');

            $result = $dialog->askConfirmation(
                $output,
                '<question>View ' . $e->getMessage() . ' would be overwritten. y/N?:</question> ',
                false
            );

            if ($result) {
                //set rewrite argument as true
                $arguments[] = true;
                call_user_func_array(array($generator, 'generateTemplate'), $arguments);
            }
        }
    }

    protected function getPath($moduleName, $directoryName)
    {
        $modulePath = $this->getApplication()->getModulePath($moduleName);

        if (!is_dir($modulePath)) {
            throw new \RuntimeException('Directory "' . $modulePath . '" not exists.');
        }

        $path = $modulePath . DIRECTORY_SEPARATOR . $directoryName;

        if (!is_dir($path)) {
            throw new \RuntimeException('Directory "' . $path . '" not exists.');
        }

        return $path;
    }

    public function verify(InputInterface $input, OutputInterface $output)
    {
        $input->setArgument(
            'moduleName',
            $this->validateModuleName($input->getArgument('moduleName'), $input, $output)
        );
        $input->setArgument(
            'controllerName',
            $this->validateControllerName($input->getArgument('controllerName'), $input, $output)
        );
    }

    protected function askControllerName($input, $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        return $dialog->askAndValidate(
            $output,
            "<question>Please enter the name of the controller:</question> \n> ",
            function ($name) use ($input, $output, $dialog) {
                return $this->validateControllerName($name, $input, $output);
            },
            true
        );
    }

    protected function askModuleName($input, $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        return $dialog->askAndValidate(
            $output,
            "<question>Please enter the name of the module:</question> \n> ",
            function ($name) use ($input, $output, $dialog) {
                return $this->validateModuleName($name, $input, $output);
            },
            true
        );
    }

    protected function validateControllerName($controllerName, InputInterface $input, OutputInterface $output)
    {
        if (!$input->isInteractive()) {
            throw new \RuntimeException('Command should be executed in interactive mode.');
        }

        if (empty($controllerName)) {
            return $this->askControllerName($input, $output);
        }

        return $controllerName;
    }

    protected function validateModuleName($moduleName, InputInterface $input, OutputInterface $output)
    {
        if (empty($moduleName)) {
            $output->writeln('');
            $output->writeln('<error>ERROR: Please enter a correct name of the module</error>');
            $output->writeln('');

            return $this->askModuleName($input, $output);
        }

        if (!$this->getApplication()->isModuleExists($moduleName)) {

            $output->writeln('');
            $output->writeln('<error>ERROR: Module ' . $moduleName . ' is not exists</error>');
            $output->writeln('');

            $dialog = $this->getHelperSet()->get('dialog');

            $result = $dialog->askConfirmation(
                $output,
                "<question>Do you want to try with another module name?. Y/n?:</question> \n> ",
                true
            );

            if (!$result) {
                //Stop script

                exit();
            }

            return $this->askModuleName($input, $output);
        }

        return $moduleName;
    }
}