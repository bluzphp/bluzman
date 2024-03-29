<?php

/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator\Template;

/**
 * RowTemplate
 *
 * @package  Bluzman\Generator\Template
 *
 * @author   Pavel Machekhin
 * @created  2013-04-16 15:35
 */

class RowTemplate extends AbstractTemplate
{
    /**
     * @var string
     */
    protected $templatePath = 'views/models/row.phtml';

    /**
     * @param array $templateData
     */
    public function setTemplateData(array $templateData)
    {
        $properties = '';
        if ($templateData['columns']) {
            $columns = $templateData['columns'];
            foreach ($columns as $column) {
                // all properties will be `string` except `bigint`, `int`, etc. columns
                $columnType = false !== strpos($column['type'], 'int') ? 'integer' : 'string';
                $properties .= ' * @property ' . $columnType . ' $' . $column['name'] . "\n";
            }
            unset($templateData['columns']);
        }
        $templateData['properties'] = $properties;
        $this->templateData = $templateData;
    }
}
