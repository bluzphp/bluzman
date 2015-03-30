<?php
/**
 * @created 2013-11-28 15:47
 * @author Pavel Machekhin <pavel.machekhin@gmail.com>
 */

namespace Bluzman\Command;

use Bluzman\Application\Application;
use Bluzman\Input\InputException;
use Bluzman\Input\InputOption;
use Bluzman\Input\InputArgument;
use Symfony\Component\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Respect;

/**
 * Class AbstractCommand
 * @package Bluzman\Command
 */
abstract class AbstractCommand extends Console\Command\Command
{
    const MAX_ATTEMPTS = 1;

    protected $validatorNamespace = 'Bluzman\Command\Validator\\';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * Set the name and description of command from the properties.
     * Add to the command the arguments and options which are defined in the inherited classes.
     */
    public function __construct()
    {
        parent::__construct($this->name);

        $this->setDescription($this->description);

        $this->registerArguments();
        $this->registerOptions();
    }

    /**
     * Adds an option.
     *
     * @param string  $name        The option name
     * @param string  $shortcut    The shortcut (can be null)
     * @param integer $mode        The option mode: One of the InputOption::VALUE_* constants
     * @param string  $description A description text
     * @param mixed   $default     The default value (must be null for InputOption::VALUE_REQUIRED or InputOption::VALUE_NONE)
     *
     * @return Command The current instance
     *
     * @api
     */
    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null, $validator = null)
    {
        $option = new InputOption($name, $shortcut, $mode, $description, $default);

        if ($validator instanceof \Respect\Validation\Validator) {
            $option->setValidator($validator);
        }

        $this->getDefinition()->addOption($option);

        return $this;
    }

    /**
     * Adds an argument.
     *
     * @param string  $name        The argument name
     * @param int     $mode        The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
     * @param string  $description A description text
     * @param mixed   $default     The default value (for InputArgument::OPTIONAL mode only)
     *
     * @return Command The current instance
     *
     * @api
     */
    public function addArgument($name, $mode = null, $description = '', $default = null, $validator = null)
    {
        $argument = new InputArgument($name, $mode, $description, $default);

        if ($validator instanceof \Respect\Validation\Validator) {
            $argument->setValidator($validator);
        }

        $this->getDefinition()->addArgument($argument);

        return $this;
    }

    /**
     * Register arguments for the command.
     */
    final protected function registerArguments()
    {
        foreach ($this->getArguments() as $argumentParams) {
            call_user_func_array(array($this, 'addArgument'), $argumentParams);
        }
    }

    /**
     * Register options for the command.
     */
    final protected function registerOptions()
    {
        foreach ($this->getOptions() as $optionParams) {
            call_user_func_array(array($this, 'addOption'), $optionParams);
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    final public function getOption($name)
    {
        /**
         * @var InputOption $defOption
         */
        $defOption = $this->getDefinition()->getOption($name);

        $optionValue = $this->getInput()->getOption($name);

        $isValid = is_null($optionValue) ? false : $defOption->validate($optionValue);

        if (!$isValid) {
            $output = $this->getOutput();

            // interact
            if (!$this->getInput()->isInteractive()) {
                throw new InputException;
            }

            /**
             * @var Console\Helper\DialogHelper $dialog
             */
            $dialog = $this->getHelperSet()->get('dialog');

            // ask user enter a valid option value
            return $dialog->askAndValidate(
                $output,
                $this->question("Please enter the " . trim(strtolower($defOption->getDescription()), ' .')),
                function ($value) use ($name, $output, $dialog, $defOption) {
                    $defOption->validate($value);

                    $this->getInput()->setOption($name, $value);

                    return $value;
                },
                1
            );
        } else {
            return $optionValue;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    final public function getArgument($name)
    {
        /**
         * @var InputOption $defArgument
         */
        $defArgument = $this->getDefinition()->getArgument($name);

        $argumentValue = $this->getInput()->getArgument($name);

        $isValid = is_null($argumentValue) ? false : $defArgument->validate($argumentValue);

        if (!$isValid) {
            $output = $this->getOutput();

            // interact
            if (!$this->getInput()->isInteractive()) {
                throw new InputException;
            }

            /**
             * @var Console\Helper\DialogHelper $dialog
             */
            $dialog = $this->getHelperSet()->get('dialog');

            // ask user enter a valid option value
            return $dialog->askAndValidate(
                $output,
                $this->question("Please enter the " . trim(strtolower($defArgument->getDescription()), ' .')),
                function ($value) use ($name, $output, $dialog, $defArgument) {
                    $defArgument->validate($value);

                    $this->getInput()->setArgument($name, $value);

                    return $value;
                },
                self::MAX_ATTEMPTS
            );
        } else {
            return $argumentValue;
        }
    }

    /**
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return integer
     */
    final public function run(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);

        return parent::run($input, $output);
    }

    /**
     * Dummy progress bar
     */
    protected function showProgress()
    {
        $progress = $this->getHelperSet()->get('progress');

        $progress->start($this->getOutput(), 50000);
        $progress->setFormat('Progress: [%bar%] %percent%%');

        // update every 500 iterations
        $progress->setRedrawFrequency(500);

        $i = 0;
        while ($i++ < 50000) {
            $progress->advance();
        }

        $progress->finish();
    }

    /**
     * @param $message
     * @return string
     */
    public function question($message)
    {
        return "<question>" . $message . ":</question> \n> ";
    }

    /**
     * @param $message
     * @return string
     */
    public function info($message)
    {
        return '<info>' . $message . '</info>';
    }

    /**
     * @param $message
     * @return string
     */
    public function error($message)
    {
        return '<error>' . $message . '</error>';
    }

    /**
     * @param $output
     */
    public function callForContribute()
    {
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln($this->error(" This command is not implemented yet. Don't be indifferent - you can contribute! https://github.com/bashmach/bluzman. "));
        $this->getOutput()->writeln('');
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }
}
