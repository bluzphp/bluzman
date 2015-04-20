<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Generator\Template;

/**
 * RowTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  2013-04-16 15:35
 */

class RowTemplate extends AbstractTemplate
{
    /**
     * @var string
     */
    protected $templatePath = 'views/row.html';

    /**
     * @param array $templateData
     */
    public function setTemplateData($templateData)
    {
        $properties = '';
        if ($templateData['columns']) {
            $columns = $templateData['columns'];
            foreach ($columns as $column) {
                // all properties will be `string` except `bigint`, `int`, etc. columns
                $columnType = preg_match('/^int/', $column['type']) ? 'integer' : 'string';
                $properties .= " * @property " . $columnType . " $" . $column['name'] . "\r\n";
            }
            unset($templateData['columns']);
        }
        $templateData['properties'] = $properties;
        $this->templateData = $templateData;
    }
}
