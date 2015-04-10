<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bashmach
 * Date: 4/16/13
 * Time: 3:34 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Bluzman\Generator\Template;

/**
 * RowTemplate
 *
 * @category Generator
 * @package  Bluzman
 *
 * @author   Pavel Machekhin
 * @created  04/16/13 3:35 PM
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
