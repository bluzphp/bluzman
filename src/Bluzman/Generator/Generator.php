<?php

namespace Bluzman\Generator;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generator
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  3/28/13 4:24 PM
 */

class Generator 
{
    const ENTITY_TYPE_CONTROLLER = 'controller';
    const ENTITY_TYPE_VIEW = 'view';
    const ENTITY_TYPE_MODEL_TABLE = 'table';
    const ENTITY_TYPE_MODEL_ROW = 'row';

    public function generateTemplate($name, $path, $templateType, $options = array(), $rewrite = false)
    {
        /**
         * @todo Do this more clean.
         */
        switch ($templateType) {
            case self::ENTITY_TYPE_CONTROLLER:
                $template = new Template\ControllerTemplate($name, $path, $options);
                break;
            case self::ENTITY_TYPE_VIEW:
                $template = new Template\ViewTemplate($name, $path, $options);
                break;
            case self::ENTITY_TYPE_MODEL_TABLE:
                $template = new Template\TableTemplate($name, $path, $options);
                break;
            case self::ENTITY_TYPE_MODEL_ROW:
                $template = new Template\RowTemplate($name, $path, $options);
                break;
            default:
                throw new \RuntimeException('Unknown entity type to generate');
                break;
        }

        $template->generate($rewrite);
    }
}