<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Generate;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate Module Structure
 *
 * @category Command
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-04-05 21:57
 */
class ModuleCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('generate:module')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('Generate a new module')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate a module structure')
        ;

        $this->addModuleArgument();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->write("Running <info>generate:module</info> command");

        $moduleName = $input->getArgument('module');

        $argument = $this->getDefinition()->getArgument('module');
        $argument->validate($moduleName);

        // create main folder and subfolders
        $this->initModuleStructure($moduleName);

        $this->verify($input, $output);

        $this->write("Module <info>$moduleName</info> has been successfully created.");
    }

    /**
     * @param $name
     */
    protected function initModuleStructure($name)
    {
        $this->addSubFolders(
            $this->getApplication()->getModulePath($name),
            [
                'controllers',
                'views'
            ]
        );
    }

    /**
     * @param $path
     * @param array $subFolders
     */
    protected function addSubFolders($path, array $subFolders = [])
    {
        if (!$this->getFs()->exists($path)) {
            $this->getFs()->mkdir($path);
        }

        foreach ($subFolders as $subFolderName) {
            $subFolderPath = $path . DIRECTORY_SEPARATOR . $subFolderName;

            $this->getFs()->mkdir($subFolderPath, 0755);
            $this->getFs()->touch([$subFolderPath . DIRECTORY_SEPARATOR . '.keep']);
        }
    }

    /**
     * @todo Revert if not verified
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function verify(InputInterface $input, OutputInterface $output)
    {
        $modulePath = $this->getApplication()->getModulePath($input->getArgument('module'));

        $paths = [
            $modulePath,
            $modulePath . DS . 'controllers',
            $modulePath . DS . 'views'
        ];

        foreach ($paths as $path) {
            if (!$this->getFs()->exists($path)) {
                return false;
            }
        }

        return true;
    }
}
