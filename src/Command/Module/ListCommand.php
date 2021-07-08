<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Command\Module;

use Bluzman\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class List command
 *
 * @package Bluzman\Command
 *
 * @author   Pavel Machekhin
 * @created  2013-03-28 14:03
 */
class ListCommand extends AbstractCommand
{
    /**
     * Command configuration
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/bluzman")
            ->setName('module:list')
            // the short description shown while running "php bin/bluzman list"
            ->setDescription('List available modules')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')
        ;
    }

    /**
     * @link https://developer.github.com/v3/#user-agent-required
     * @link https://developer.github.com/v3/search/#search-repositories
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $curl,
            CURLOPT_URL,
            'https://api.github.com/search/repositories?q=topic:bluz-module+org:bluzphp'
        );
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            [
                'Accept: application/vnd.github.v3+json',
                'User-Agent: Bluzphp-Bluzman'
            ]
        );
        $repositories = curl_exec($curl);
        curl_close($curl);

        if (!$repositories) {
            $this->error('ERROR: Network problems, try again later');
            return 1;
        }

        $repositories = json_decode($repositories);

        if (!$repositories) {
            $this->error('ERROR: Invalid GitHub response');
            return 2;
        }

        if (!$repositories->total_count) {
            $this->error('ERROR: Not found any modules');
            return 3;
        }

        $this->write('List of modules (<info>installed</info>, <comment>available</comment>):');


        $repoData = [];

        foreach ($repositories->items as $repo) {
            $module = substr($repo->name, 7);
            $repoData[$module] = $repo->full_name;
        }

        ksort($repoData);

        foreach ($repoData as $module => $name) {
            if ($this->getApplication()->isModuleExists($module)) {
                $this->write(" - <info>$module</info> [$name]");
            } else {
                $this->write(" - <comment>$module</comment> [$name]");
            }
        }

        $this->write('You can install new module with <info>bluzman module:install name</info> command');
        return 0;
    }
}
